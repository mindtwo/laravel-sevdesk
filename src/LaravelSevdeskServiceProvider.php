<?php

namespace mindtwo\LaravelSevdesk;

use mindtwo\LaravelSevdesk\Commands\LaravelSevdeskCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelSevdeskServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-sevdesk')
            ->hasConfigFile()
            ->hasMigration('add_sevdesk_customer_id_column_to_table')
            ->hasCommand(LaravelSevdeskCommand::class);
    }
}
