<?php

namespace Tapp\FilamentFormBuilder\Filament\Resources\FilamentFormResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Tapp\FilamentFormBuilder\Filament\Resources\FilamentFormResource;

class EditFilamentForm extends EditRecord
{
    protected static string $resource = FilamentFormResource::class;

    public function getTitle(): string
    {
        return 'Edit '.config('filament-form-builder.admin-panel-resource-name');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('preview')
                ->visible(fn () => (bool) config('filament-form-builder.preview-route'))
                ->url(fn ($record) => route(config('filament-form-builder.preview-route'), ['form' => $record->id]))
                ->openUrlInNewTab(),
        ];
    }
}
