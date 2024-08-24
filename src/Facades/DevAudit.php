<?php

namespace jamesclark32\DevAudit\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \jamesclark32\DevAudit\DevAudit
 */
class DevAudit extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \jamesclark32\DevAudit\DevAudit::class;
    }
}
