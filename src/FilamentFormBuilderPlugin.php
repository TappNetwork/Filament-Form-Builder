<?php

namespace Tapp\FilamentFormBuilder;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentFormBuilderPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-form-builder';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources(
                config('filament-form-builder.resources')
            );
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
