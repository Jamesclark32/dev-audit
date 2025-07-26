<?php

namespace JamesClark32\DevAudit\Commands;

use Illuminate\Console\Command;
use JamesClark32\DevAudit\Helpers\FailureFeedbackHelper;
use JamesClark32\DevAudit\Helpers\OutputFormatHelper;
use JamesClark32\DevAudit\Helpers\ProgressBarHelper;
use JamesClark32\DevAudit\Helpers\SpinnerHelper;
use JamesClark32\DevAudit\Helpers\TableHelper;
use JamesClark32\DevAudit\Models\AuditModel;
use Symfony\Component\Process\Process;

class DevAuditCommand extends Command
{
    protected $signature = 'dev:audit {--lint}';

    protected $description = 'Executes audits against the code base';

    private FailureFeedbackHelper $failureFeedbackHelper;

    private OutputFormatHelper $outputFormatHelper;

    private ProgressBarHelper $progressBarHelper;

    private SpinnerHelper $spinner;

    private TableHelper $tableHelper;

    private array $audits = [];

    private bool $hasUi = false;

    public function __construct(ProgressBarHelper $progressBarHelper, SpinnerHelper $spinner, TableHelper $tableHelper, FailureFeedbackHelper $failureFeedbackHelper, OutputFormatHelper $outputFormatHelper)
    {
        $this->progressBarHelper = $progressBarHelper;
        $this->spinner = $spinner;
        $this->tableHelper = $tableHelper;
        $this->failureFeedbackHelper = $failureFeedbackHelper;
        $this->outputFormatHelper = $outputFormatHelper;

        foreach (config('dev-audit.audits') as $audits) {
            $auditInstance = new AuditModel;
            $auditInstance->title = data_get($audits, 'title');
            $auditInstance->command = data_get($audits, 'command');
            $auditInstance->failureHint = data_get($audits, 'failure_hint');
            $this->audits[] = $auditInstance;
        }

        parent::__construct();
    }

    public function handle(): int
    {
        $outputInterface = $this->output->getOutput();

        if ($this->option('lint') || config('dev-audit.settings.always_lint') === true) {
            $this->line($this->outputFormatHelper->buildInlineOutput('Linters', 'bright-white', ['bold']));
            $this->call('dev:lint');
            $this->newLine();
            $this->line($this->outputFormatHelper->buildInlineOutput('Audits', 'bright-white', ['bold']));

        }

        if (method_exists($outputInterface, 'section')) {

            $output = $this->output->getOutput();
            if (method_exists($output, 'section')) {
                $this->hasUi = true;
                $tableSection = $output->section();
                $progressBarSection = $output->section();
                $this->tableHelper->buildTable($tableSection, $this->audits);
                $this->progressBarHelper->buildProgressBar($progressBarSection, $this->audits, $this->outputFormatHelper);
            }
        }

        $this->processActions();

        if ($this->hasUi) {
            $this->progressBarHelper->drawCompleted($this->outputFormatHelper);
        }

        $this->output->write($this->failureFeedbackHelper->buildFailures($this->audits, $this->outputFormatHelper));

        foreach ($this->audits as $action) {
            if ($action->hadErrors === true) {
                return self::FAILURE;
            }
        }

        return self::SUCCESS;
    }

    protected function processActions(): void
    {
        $count = 0;
        foreach ($this->audits as $action) {
            $count++;
            $this->processAction($action, $count);
        }
    }

    protected function processAction(AuditModel $action, int $count): void
    {
        $action->isRunning = true;
        if ($this->hasUi) {
            $this->tableHelper->redrawTable($this->audits);
        }

        $process = $this->executeAction($action, $count);

        $action->isRunning = false;
        $action->hasCompleted = true;
        $action->hadErrors = ! $process->isSuccessful();
        $action->output = $process->getOutput();
        $action->errorOutput = $process->getErrorOutput();

        if ($this->hasUi) {
            $this->tableHelper->redrawTable($this->audits);
        }
    }

    protected function executeAction(AuditModel $action, int $count): Process
    {
        $envVars = $_ENV;

        $envTestingPath = base_path('.env.testing');
        if (file_exists($envTestingPath)) {
            $lines = file($envTestingPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && ! str_starts_with($line, '#')) {
                    [$key, $value] = explode('=', $line, 2);
                    $envVars[trim($key)] = trim($value);
                }
            }
        } else {
            $envVars['APP_ENV'] = 'testing';
        }

        $process = Process::fromShellCommandline($action->command, base_path(), $envVars);
        $process->start();

        if ($this->hasUi) {
            while ($process->isRunning()) {
                $this->progressBarHelper->redrawProgressBar($count, $action->title, $this->spinner->spin(), $this->outputFormatHelper);
                time_nanosleep(0, 250000000);
            }
        }

        return $process;
    }
}
