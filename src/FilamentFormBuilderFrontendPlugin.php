<?php

namespace Tapp\FilamentFormBuilder;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentFormBuilderFrontendPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-form-builder-frontend';
    }

    public function register(Panel $panel): void
    {
        // Register pages for viewing forms and entries in the app panel
        $formPageClass = config('filament-form-builder.app-panel-form-page-class');
        $entryPageClass = config('filament-form-builder.app-panel-entry-page-class');

        // Use package defaults if not configured
        if (! $formPageClass) {
            $formPageClass = \Tapp\FilamentFormBuilder\Filament\Pages\ShowForm::class;
        }

        if (! $entryPageClass) {
            $entryPageClass = \Tapp\FilamentFormBuilder\Filament\Pages\ShowEntry::class;
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
