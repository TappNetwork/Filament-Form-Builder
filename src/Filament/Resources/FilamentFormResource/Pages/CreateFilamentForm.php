<?php

namespace Tapp\FilamentFormBuilder\Filament\Resources\FilamentFormResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Tapp\FilamentFormBuilder\Filament\Resources\FilamentFormResource;

class CreateFilamentForm extends CreateRecord
{
    protected static string $resource = FilamentFormResource::class;

    public function getTitle(): string
    {
        return 'Create '.config('filament-forms.admin-panel-resource-name');
    }
}
