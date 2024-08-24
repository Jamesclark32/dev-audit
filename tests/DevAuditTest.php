<?php

namespace JamesClark32\DevAudit\Tests;

class DevAuditTest extends TestCase
{
    public function test_huh()
    {
        $this
            ->withoutMockingConsoleOutput()
            ->artisan('dev:audit')
            ->assertExitCode(0);
    }
}
