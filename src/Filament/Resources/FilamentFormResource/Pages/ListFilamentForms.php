<?php

namespace Tapp\FilamentForms\Filament\Resources\FilamentFormResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Tapp\FilamentForms\Filament\Resources\FilamentFormResource;

class ListFilamentForms extends ListRecords
{
    protected static string $resource = FilamentFormResource::class;

    public function getTitle(): string
    {
        return config('filament-forms.admin-panel-resource-name');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
