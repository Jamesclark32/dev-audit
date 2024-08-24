<?php

namespace JamesClark32\DevAudit\Helpers;

class SpinnerHelper
{
    private array $sequence = [
        '.   ',
        '..  ',
        '... ',
        '... ',
        '....',
        '..  ',
        '.   ',
        ' .  ',
        '  . ',
        '   .',
        '  ..',
        ' ...',
        '....',
        ' ...',
        '  ..',
        '   .',
        '  . ',
        ' .  ',
    ];

    private int $step = 0;

    public function spin(): string
    {
        $spinner = $this->getSpinner();
        $this->incrementStep();

        return $spinner;
    }

    public function getSpinner(): string
    {
        return data_get($this->sequence, $this->step);
    }

    public function incrementStep(): void
    {
        $this->step++;
        if ($this->step >= count($this->sequence)) {
            $this->step = 0;
        }

    }
}
