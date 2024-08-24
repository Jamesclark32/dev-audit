<?php

namespace JamesClark32\DevAudit\Tests;

use JamesClark32\DevAudit\DevAuditServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            DevAuditServiceProvider::class,
        ];
    }
}
