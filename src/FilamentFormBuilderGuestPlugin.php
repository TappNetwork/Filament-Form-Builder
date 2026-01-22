<?php

namespace Tapp\FilamentFormBuilder;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\Facades\Route;

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
        // Guest panel doesn't need admin resources, just needs styles
        // Styles are included via the Vite theme in the panel provider
    }

    public function boot(Panel $panel): void
    {
        // Route is registered in GuestPanelProvider::routes() to ensure proper panel context
    }
}
