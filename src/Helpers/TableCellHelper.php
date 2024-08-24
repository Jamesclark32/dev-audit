<?php

namespace JamesClark32\DevAudit\Helpers;

use Symfony\Component\Console\Helper\TableCell as SymfonyTableCell;
use Symfony\Component\Console\Helper\TableCellStyle;

class TableCellHelper
{
    public function buildCell(string $text, ?string $color = 'white', ?string $align = 'left'): SymfonyTableCell
    {
        return new SymfonyTableCell(
            $text,
            [
                'style' => new TableCellStyle([
                    'align' => $align,
                    'fg' => $color,
                ]),
            ]
        );
    }
}
