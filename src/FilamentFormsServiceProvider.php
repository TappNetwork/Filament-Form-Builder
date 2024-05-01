<?php

namespace Tapp\FilamentForms;

use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Tapp\FilamentForms\Livewire\FilamentForm\Show as FilamentFormShow;
use Tapp\FilamentForms\Livewire\FilamentFormUser\Show as FilamentFormUserShow;

class FilamentFormsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-forms';

    public function configurePackage(Package $package): void
    {
        $package->name('filament-forms')
            ->hasConfigFile('filament-forms')
            ->hasViews('filament-forms');
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'filament-forms');

        Livewire::component('tapp.filament-forms.livewire.filament-form.show', FilamentFormShow::class);
        Livewire::component('tapp.filament-forms.livewire.filament-form-user.show', FilamentFormUserShow::class);
    }

    public function packageBooted(): void
    {
        parent::packageBooted();
    }
}
