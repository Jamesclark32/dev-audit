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
    protected $signature = 'dev:audit';

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
        $process = Process::fromShellCommandline($action->command, null, ['APP_ENV' => 'testing']);
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
