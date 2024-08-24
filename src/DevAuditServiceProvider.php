<?php

namespace JamesClark32\DevAudit;

use JamesClark32\DevAudit\Commands\DevAuditCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DevAuditServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('dev-audit')
            ->hasConfigFile()
            ->hasCommand(DevAuditCommand::class);
    }
}
