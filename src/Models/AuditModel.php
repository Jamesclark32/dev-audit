<?php

namespace JamesClark32\DevAudit\Models;

use JamesClark32\DevAudit\Helpers\TableCellHelper;
use JamesClark32\DevAudit\Helpers\TableRowHelper;

class AuditModel
{
    public string $title;

    public string $command;

    public string $status = 'Pending';

    public string $color = 'white';

    public string $output;

    public string $errorOutput;

    public ?string $failureHint;

    public bool $hadErrors = false;

    public bool $isRunning = false;

    public bool $hasCompleted = false;

    private TableCellHelper $tableCellHelper;

    private TableRowHelper $tableRowHelper;

    public function __construct()
    {
        $this->tableCellHelper = new TableCellHelper;
        $this->tableRowHelper = new TableRowHelper($this->tableCellHelper);
    }

    public function updateStatus(): void
    {
        if ($this->isRunning === false && $this->hasCompleted === false) {
            $this->status = '◦ Queued';
        } elseif ($this->isRunning === true && $this->hasCompleted === false) {
            $this->status = '➞ Active';
        } elseif ($this->hasCompleted === true && $this->hadErrors === false) {
            $this->status = '✓ Passed';
        } elseif ($this->hasCompleted === true && $this->hadErrors === true) {
            $this->status = '☓ Failed';
        }
    }

    public function updateColor(): void
    {
        if ($this->isRunning === false && $this->hasCompleted === false) {
            $this->color = 'white';
        } elseif ($this->isRunning === true && $this->hasCompleted === false) {
            $this->color = 'yellow';
        } elseif ($this->hasCompleted === true && $this->hadErrors === false) {
            $this->color = 'green';
        } elseif ($this->hasCompleted === true && $this->hadErrors === true) {
            $this->color = 'red';
        }
    }

    public function toTableRow(): array
    {
        $this->updateStatus();
        $this->updateColor();

        return $this->tableRowHelper->buildRow([
            [
                'text' => $this->title,
                'color' => $this->color,
            ],
            [
                'text' => $this->command,
                'color' => $this->color,
                'alignment' => 'left',
            ],
            [
                'text' => $this->status,
                'color' => $this->color,
            ],
        ]);
    }
}
