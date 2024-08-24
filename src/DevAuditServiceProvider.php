<?php

namespace jamesclark32\DevAudit;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use jamesclark32\DevAudit\Commands\DevAuditCommand;

class DevAuditServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('dev-audit')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_dev_audit_table')
            ->hasCommand(DevAuditCommand::class);
    }
}
