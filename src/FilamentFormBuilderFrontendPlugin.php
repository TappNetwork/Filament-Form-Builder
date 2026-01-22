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
        // Register pages for viewing forms in the app panel
        $panel->pages([
            \App\Filament\App\Pages\ShowForm::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
