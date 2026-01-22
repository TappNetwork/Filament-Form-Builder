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
        $pageClass = config('filament-form-builder.app-panel-form-page-class');

        if ($pageClass && class_exists($pageClass)) {
            $panel->pages([
                $pageClass,
            ]);
        }
    }

    public function boot(Panel $panel): void
    {
        // Entry route is registered in FilamentFormBuilderServiceProvider with SetFormPanel middleware
    }
}
