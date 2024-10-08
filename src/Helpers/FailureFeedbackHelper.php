<?php

namespace JamesClark32\DevAudit\Helpers;

use JamesClark32\DevAudit\Models\AuditModel;
use Symfony\Component\Console\Terminal;

class FailureFeedbackHelper
{
    public function buildFailures(array $actions, OutputFormatHelper $outputFormatHelper): array
    {
        $failures = [];
        foreach ($actions as $action) {
            if ($action->hadErrors === true) {
                $failures[] = $this->buildFailure($action, $outputFormatHelper);
            }
        }

        return $failures;
    }

    protected function buildFailure(AuditModel $action, OutputFormatHelper $outputFormatHelper): string
    {
        $terminal = new Terminal;

        $failure = PHP_EOL.str_repeat('─', $terminal->getWidth()).PHP_EOL;
        $failure .= $outputFormatHelper->buildInlineOutput($action->title, 'bright-white', ['bold']);
        $failure .= $outputFormatHelper->buildOutput(' failure details', 'gray');

        foreach ([
            'Command' => $action->command,
            'Output' => $action->output,
            'Error Output' => $action->errorOutput,
            'Advice' => $action->failureHint,
        ] as $title => $value) {
            $failure .= $this->buildSection($title, $value, $outputFormatHelper);
        }

        return $failure.PHP_EOL;
    }

    protected function buildSection(string $title, ?string $value, OutputFormatHelper $outputFormatHelper): string
    {
        $section = PHP_EOL;
        $section .= $outputFormatHelper->buildOutput($title, 'blue', ['bold', 'underscore']);

        if ($value) {
            return $section.PHP_EOL.$value.PHP_EOL.PHP_EOL;
        }

        return $section.PHP_EOL.$outputFormatHelper->buildOutput('n/a', 'gray').PHP_EOL;
    }
}
