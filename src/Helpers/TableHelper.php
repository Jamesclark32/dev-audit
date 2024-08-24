<?php

namespace JamesClark32\DevAudit\Helpers;

use Symfony\Component\Console\Helper\Table as SymfonyTable;
use Symfony\Component\Console\Output\ConsoleSectionOutput;

class TableHelper
{
    private SymfonyTable $table;

    private ConsoleSectionOutput $section;

    public function buildTable(ConsoleSectionOutput $section, array $actions): void
    {
        $this->section = $section;
        $this->table = new SymfonyTable($this->section);
        $this->table->setHeaders(['Title', 'Command', 'Outcome']);

        $this->redrawTable($actions);
    }

    public function redrawTable(array $actions): void
    {
        $tableRows = [];
        foreach ($actions as $action) {
            $tableRows[] = $action->toTableRow();
        }
        $this->section->clear();
        $this->table->setRows($tableRows);
        $this->table->render();
    }
}
