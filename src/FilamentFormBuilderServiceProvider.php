<?php

namespace Tapp\FilamentFormBuilder;

use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Tapp\FilamentFormBuilder\Livewire\FilamentForm\Form as FilamentForm;
use Tapp\FilamentFormBuilder\Livewire\FilamentForm\Show as FilamentFormShow;
use Tapp\FilamentFormBuilder\Livewire\FilamentFormUser\Entry as FilamentFormUserEntry;
use Tapp\FilamentFormBuilder\Livewire\FilamentFormUser\Show as FilamentFormUserShow;
use Tapp\FilamentFormBuilder\Models\FilamentFormUser;
use Tapp\FilamentFormBuilder\Observers\FilamentFormUserObserver;

class FilamentFormBuilderServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-form-builder';

    protected array $styles = [
        'filament-form-builder' => __DIR__.'/../dist/filament-form-builder.css',
    ];

    public function configurePackage(Package $package): void
    {
        $package->name('filament-form-builder')
            ->hasMigration('create_dynamic_filament_form_tables')
            ->hasMigration('add_schema_to_filament_form_fields')
            ->hasMigration('add_notification_emails_to_filament_forms_table')
            ->hasConfigFile('filament-form-builder')
            ->hasRoute('routes')
            ->hasViews('filament-form-builder');
    }

    public function boot()
    {
        parent::boot();

        // Register the original components
        Livewire::component('tapp.filament-form-builder.livewire.filament-form.show', FilamentFormShow::class);
        Livewire::component('tapp.filament-form-builder.livewire.filament-form-user.show', FilamentFormUserShow::class);

        // Register the new layout components
        Livewire::component('tapp.filament-form-builder.livewire.filament-form.form', FilamentForm::class);
        Livewire::component('tapp.filament-form-builder.livewire.filament-form-user.entry', FilamentFormUserEntry::class);

        // Register observer for form submission notifications
        FilamentFormUser::observe(FilamentFormUserObserver::class);
    }

    public function packageBooted(): void
    {
        parent::packageBooted();
    }
}
