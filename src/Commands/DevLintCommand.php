<?php

namespace JamesClark32\DevAudit\Commands;

use Illuminate\Console\Command;
use JamesClark32\DevAudit\Helpers\OutputFormatHelper;
use JamesClark32\DevAudit\Helpers\ProgressBarHelper;
use JamesClark32\DevAudit\Helpers\SpinnerHelper;
use JamesClark32\DevAudit\Helpers\TableHelper;
use JamesClark32\DevAudit\Models\LinterModel;
use Symfony\Component\Process\Process;

class DevLintCommand extends Command
{
    protected $signature = 'dev:lint';

    protected $description = 'Executes linters against the code base';

    private OutputFormatHelper $outputFormatHelper;

    private ProgressBarHelper $progressBarHelper;

    private SpinnerHelper $spinner;

    private TableHelper $tableHelper;

    private array $linters = [];

    private bool $hasUi = false;

    public function __construct(ProgressBarHelper $progressBarHelper, SpinnerHelper $spinner, TableHelper $tableHelper, OutputFormatHelper $outputFormatHelper)
    {
        $this->progressBarHelper = $progressBarHelper;
        $this->spinner = $spinner;
        $this->tableHelper = $tableHelper;
        $this->outputFormatHelper = $outputFormatHelper;

        foreach (config('dev-audit.linters') as $linters) {
            $linterInstance = new LinterModel;
            $linterInstance->title = data_get($linters, 'title');
            $linterInstance->command = data_get($linters, 'command');
            $this->linters[] = $linterInstance;
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
                $this->tableHelper->buildTable($tableSection, $this->linters);
                $this->progressBarHelper->buildProgressBar($progressBarSection, $this->linters, $this->outputFormatHelper);
            }
        }

        $this->processLinters();

        if ($this->hasUi) {
            $this->progressBarHelper->drawCompleted($this->outputFormatHelper);
        }

        foreach ($this->linters as $linter) {
            if ($linter->errorOutput) {
                $this->line($this->outputFormatHelper->buildOutput($linter->title.' Error', 'blue', ['bold', 'underscore']));
                $this->output->write($linter->errorOutput);
            }
        }

        foreach ($this->linters as $linter) {
            if ($linter->hadErrors === true) {
                return self::FAILURE;
            }
        }

        return self::SUCCESS;
    }

    protected function processLinters(): void
    {
        $count = 0;
        foreach ($this->linters as $linter) {
            $count++;
            $this->processLinter($linter, $count);
        }
    }

    protected function processLinter(LinterModel $linter, int $count): void
    {
        $linter->isRunning = true;
        if ($this->hasUi) {
            $this->tableHelper->redrawTable($this->linters);
        }

        $process = $this->executeLinter($linter, $count);

        $linter->isRunning = false;
        $linter->hasCompleted = true;
        $linter->hadErrors = ! $process->isSuccessful();
        $linter->output = $process->getOutput();
        $linter->errorOutput = $process->getErrorOutput();

        if ($this->hasUi) {
            $this->tableHelper->redrawTable($this->linters);
        }
    }

    protected function executeLinter(LinterModel $linter, int $count): Process
    {
        $process = Process::fromShellCommandline($linter->command, null, ['APP_ENV' => 'testing']);
        $process->start();

        if ($this->hasUi) {
            while ($process->isRunning()) {
                $this->progressBarHelper->redrawProgressBar($count, $linter->title, $this->spinner->spin(), $this->outputFormatHelper);
                time_nanosleep(0, 250000000);
            }
        }

        return $process;
    }
}
