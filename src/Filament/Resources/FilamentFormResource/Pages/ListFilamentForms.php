<?php

namespace Tapp\FilamentForms\Filament\Resources\FilamentFormResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Tapp\FilamentForms\Filament\Resources\FilamentFormResource;

class ListFilamentForms extends ListRecords
{
    protected static string $resource = FilamentFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
