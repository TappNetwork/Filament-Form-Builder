<?php

namespace Tapp\FilamentFormBuilder;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentFormsPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-forms';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources(
                config('filament-forms.resources')
            );
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
