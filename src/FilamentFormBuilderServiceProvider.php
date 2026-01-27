<?php

namespace Tapp\FilamentFormBuilder;

use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Tapp\FilamentFormBuilder\Livewire\FilamentForm\Form as FilamentForm;
use Tapp\FilamentFormBuilder\Livewire\FilamentForm\Show as FilamentFormShow;
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
            ->hasMigration('change_label_to_text_in_filament_form_fields')
            ->hasConfigFile('filament-form-builder')
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

        // Register observer for form submission notifications
        FilamentFormUser::observe(FilamentFormUserObserver::class);

        // Register the form route globally so it's available when the model accesses it
        // The SetFormPanel middleware ensures the correct panel context is set based on authentication
        $middlewareClass = config('filament-form-builder.set-form-panel-middleware-class');

        // Use package default middleware if not configured
        if (! $middlewareClass) {
            $middlewareClass = \Tapp\FilamentFormBuilder\Http\Middleware\SetFormPanel::class;
        }

        $middleware = ['web', $middlewareClass];

        // Get the page classes (use package defaults if not configured)
        $formPageClass = config('filament-form-builder.guest-panel-form-page-class')
            ?? \Tapp\FilamentFormBuilder\Filament\Pages\ShowForm::class;
        $entryPageClass = config('filament-form-builder.guest-panel-entry-page-class')
            ?? \Tapp\FilamentFormBuilder\Filament\Pages\ShowEntry::class;

        Route::middleware($middleware)->group(function () use ($formPageClass, $entryPageClass) {
            Route::get(
                config('filament-form-builder.filament-form-uri').'/{form}',
                $formPageClass
            )->name('filament-form-builder.show');

            Route::get(
                config('filament-form-builder.filament-form-user-uri').'/{entry}',
                $entryPageClass
            )->name('filament-form-users.show');
        });
    }

    public function packageBooted(): void
    {
        parent::packageBooted();
    }
}
