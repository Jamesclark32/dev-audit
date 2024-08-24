<?php

namespace jamesclark32\DevAudit\Commands;

use Illuminate\Console\Command;

class DevAuditCommand extends Command
{
    public $signature = 'dev-audit';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
