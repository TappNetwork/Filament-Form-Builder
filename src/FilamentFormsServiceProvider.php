<?php

namespace Tapp\FilamentForms;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentSurveyServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-forms';

    public function configurePackage(Package $package): void
    {
        $package->name('filament-forms')
            ->hasConfigFile('filament-forms')
            ->hasViews('filament-forms');
    }

    public function packageBooted(): void
    {
        parent::packageBooted();
    }
}
