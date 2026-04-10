<?php

namespace Tapp\FilamentFormBuilder;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\Facades\Route;
use Tapp\FilamentFormBuilder\Filament\Pages\ShowEntry;
use Tapp\FilamentFormBuilder\Filament\Pages\ShowForm;

class FilamentFormBuilderGuestPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-form-builder-guest';
    }

    public function register(Panel $panel): void
    {
        // Register pages for viewing forms and entries in the guest panel
        $formPageClass = config('filament-form-builder.guest-panel-form-page-class');
        $entryPageClass = config('filament-form-builder.guest-panel-entry-page-class');

        // Use package defaults if not configured
        if (! $formPageClass) {
            $formPageClass = ShowForm::class;
        }

        if (! $entryPageClass) {
            $entryPageClass = ShowEntry::class;
        }

        $panel->pages([
            $formPageClass,
            $entryPageClass,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // Entry route is registered in FilamentFormBuilderServiceProvider with SetFormPanel middleware
    }
}
