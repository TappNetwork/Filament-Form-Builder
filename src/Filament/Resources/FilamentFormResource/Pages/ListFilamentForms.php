<?php

namespace Tapp\FilamentFormBuilder\Filament\Resources\FilamentFormResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Tapp\FilamentFormBuilder\Filament\Resources\FilamentFormResource;

class ListFilamentForms extends ListRecords
{
    protected static string $resource = FilamentFormResource::class;

    public function getTitle(): string
    {
        return config('filament-form-builder.admin-panel-resource-name-plural');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Create '.config('filament-form-builder.admin-panel-resource-name')),
        ];
    }
}
