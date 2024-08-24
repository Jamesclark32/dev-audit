<?php

namespace JamesClark32\DevAudit\Helpers;

class TableRowHelper
{
    private TableCellHelper $tableCellHelper;

    public function __construct(TableCellHelper $tableCellHelper)
    {
        $this->tableCellHelper = $tableCellHelper;
    }

    public function buildRow(array $rowCells): array
    {
        $cells = [];
        foreach ($rowCells as $rowCell) {
            $cells[] = $this->tableCellHelper->buildCell(data_get($rowCell, 'text'), data_get($rowCell, 'color', data_get($rowCell, 'alignment')));
        }

        return $cells;
    }
}
