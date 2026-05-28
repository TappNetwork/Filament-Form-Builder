<?php

declare(strict_types=1);

namespace Tapp\FilamentFormBuilder\Filament\Infolists;

use Filament\Actions\Action;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Tapp\FilamentFormBuilder\Models\FilamentFormUser;

final class FilamentFormUserEntryInfolist
{
    public static function configure(Schema $schema, bool $dense = false): Schema
    {
        $schema = $schema
            ->columns(1)
            ->schema([
                TextEntry::make('user.name')
                    ->label('Name')
                    ->columnSpanFull()
                    ->visible(fn (FilamentFormUser $record): bool => $record->user_id !== null),
                TextEntry::make('filamentForm.name')
                    ->label('Form Name')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label('Form Completed At')
                    ->dateTime()
                    ->columnSpanFull(),
                KeyValueEntry::make('key_value_entry')
                    ->label('Form Entry')
                    ->columnSpanFull()
                    ->keyLabel('Question')
                    ->valueLabel('Answer'),
                RepeatableEntry::make('media')
                    ->label('Uploaded Files')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('custom_properties.field_label')
                            ->label('Question')
                            ->columnSpanFull(),
                        TextEntry::make('custom_properties.original_name')
                            ->label('File Name')
                            ->columnSpanFull()
                            ->suffixAction(
                                Action::make('download')
                                    ->icon('heroicon-o-arrow-down-tray')
                                    ->action(function ($record) {
                                        return response()->download(
                                            $record->getPath(),
                                            $record->getCustomProperty('original_name')
                                        );
                                    })
                            ),
                    ])
                    ->columns(1)
                    ->state(fn (FilamentFormUser $record) => $record->getMedia())
                    ->visible(fn (FilamentFormUser $record): bool => $record->getMedia()->isNotEmpty()),
            ]);

        if ($dense) {
            $schema->dense();
        }

        return $schema;
    }
}
