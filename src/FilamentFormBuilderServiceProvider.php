<?php

namespace Tapp\FilamentFormBuilder;

use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Tapp\FilamentFormBuilder\Livewire\FilamentForm\Show as FilamentFormShow;
use Tapp\FilamentFormBuilder\Livewire\FilamentFormUser\Show as FilamentFormUserShow;

class FilamentFormBuilderServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-form-builder';

    public function configurePackage(Package $package): void
    {
        $package->name('filament-form-builder')
            ->hasMigration('create_dynamic_filament_form_tables')
            ->hasConfigFile('filament-form-builder')
            ->hasViews()
            ->hasRoute('routes')
            ->hasViews('filament-form-builder');
    }

    public function boot()
    {
        parent::boot();
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'filament-form-builder');

        Livewire::component('tapp.filament-form-builder.livewire.filament-form.show', FilamentFormShow::class);
        Livewire::component('tapp.filament-form-builder.livewire.filament-form-user.show', FilamentFormUserShow::class);
    }

    public function packageBooted(): void
    {
        parent::packageBooted();
    }
}
