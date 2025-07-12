<?php

namespace Tapp\FilamentFormBuilder\Livewire\FilamentFormUser;

use Filament\Schemas\Schema;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Livewire\Component;
use Tapp\FilamentFormBuilder\Models\FilamentFormUser;

class Show extends Component implements HasForms, HasInfolists
{
    use InteractsWithForms;
    use InteractsWithInfolists;

    public FilamentFormUser $entry;

    public function mount(FilamentFormUser $entry): void
    {
        $this->entry = $entry->load('user', 'filamentForm');
    }

    public function entryInfoList(Schema $schema): Schema
    {
        return $schema
            ->record($this->entry)
            ->schema([
                TextEntry::make('user.name'),
                TextEntry::make('filamentForm.name')
                    ->label('Form Name'),
                TextEntry::make('created_at')
                    ->label('Form Completed At')
                    ->dateTime(),
                KeyValueEntry::make('key_value_entry')
                    ->label('Form Entry')
                    ->keyLabel('Question')
                    ->valueLabel('Answer'),
                RepeatableEntry::make('media')
                    ->label('Uploaded Files')
                    ->schema([
                        TextEntry::make('custom_properties.field_label')
                            ->label('Question'),
                        TextEntry::make('custom_properties.original_name')
                            ->label('File Name')
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
                    ->state(function () {
                        return $this->entry->getMedia();
                    })
                    ->visible(fn () => $this->entry->getMedia()->isNotEmpty()),
            ]);
    }

    public function render()
    {
        /** @phpstan-ignore-next-line */
        return view('filament-form-builder::livewire.filament-form-user.show');
    }
}
