<?php

declare(strict_types=1);

namespace Tapp\FilamentFormBuilder\Support;

use Tapp\FilamentFormBuilder\Models\FilamentFormUser;

final class FormEntryUrl
{
    public static function show(FilamentFormUser $entry): string
    {
        $routeName = config('filament-form-builder.filament-form-user-show-route', 'filament-form-users.show');

        return route($routeName, ['entry' => $entry], false);
    }
}
