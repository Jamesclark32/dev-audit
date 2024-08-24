<?php

namespace JamesClark32\DevAudit\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JamesClark32\DevAudit\DevAudit
 */
class DevAudit extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \JamesClark32\DevAudit\DevAudit::class;
    }
}
