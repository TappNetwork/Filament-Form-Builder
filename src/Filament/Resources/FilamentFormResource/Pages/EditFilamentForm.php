<?php

namespace Tapp\FilamentForms\Filament\Resources\FilamentFormResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Tapp\FilamentForms\Filament\Resources\FilamentFormResource;

class EditFilamentForm extends EditRecord
{
    protected static string $resource = FilamentFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
