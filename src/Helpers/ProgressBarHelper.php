<?php

namespace JamesClark32\DevAudit\Helpers;

use Symfony\Component\Console\Output\ConsoleSectionOutput;

class ProgressBarHelper
{
    private ConsoleSectionOutput $section;

    private int $totalCount;

    public function buildProgressBar(ConsoleSectionOutput $section, array $actions, OutputFormatHelper $outputFormatHelper): void
    {
        $this->section = $section;
        $this->totalCount = count($actions);

        $this->redrawProgressBar(0, '---', '.', $outputFormatHelper);
    }

    public function redrawProgressBar(int $actionNumber, string $actionTitle, string $spinner, OutputFormatHelper $outputFormatHelper): void
    {
        $this->section->clear();
        $this->section->overwrite($this->drawProgressBar($actionNumber, $actionTitle, $spinner, $outputFormatHelper));
    }

    public function drawProgressBar(int $actionNumber, string $actionTitle, string $spinner, OutputFormatHelper $outputFormatHelper): string
    {
        $progressBarOutput = $outputFormatHelper->buildInlineOutput("$spinner running audit $actionNumber/$this->totalCount", 'bright-blue');
        $progressBarOutput .= $outputFormatHelper->buildOutput(" $actionTitle", 'bright-white');

        return $progressBarOutput;
    }

    public function drawCompleted(OutputFormatHelper $outputFormatHelper): void
    {
        $this->section->clear();
        $this->section->overwrite($outputFormatHelper->buildOutput(".... All $this->totalCount audits completed", 'blue'));
    }
}
