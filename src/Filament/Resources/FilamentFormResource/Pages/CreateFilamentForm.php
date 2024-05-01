<?php

namespace Tapp\FilamentForms\Filament\Resources\FilamentFormResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Tapp\FilamentForms\Filament\Resources\FilamentFormResource;

class CreateFilamentForm extends CreateRecord
{
    protected static string $resource = FilamentFormResource::class;

    public function getTitle(): string
    {
        return 'Create '.config('filament-forms.admin-panel-resource-name');
    }
}
