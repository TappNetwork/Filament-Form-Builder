<?php

namespace Tapp\FilamentForms\Livewire\FilamentFormUser;

use Livewire\Component;
use Tapp\FilamentForms\Models\FilamentFormUser;
use Filament\Infolists\Infolist;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;

class DynamicEntry extends Component implements HasForms, HasInfolists
{
    use InteractsWithInfolists;
    use InteractsWithForms;

    public FilamentFormUser $entry;

    public function mount(FilamentFormUser $entry): void
    {
        $this->entry = $entry->load('user', 'filamentForm');

        $this->entry->firstEntry = $entry->entry[0];
    }

    public function entryInfoList(Infolist $infolist): Infolist
    {
        // dd($this->entry);
        return $infolist
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

            ]);
    }

    public function render()
    {
        return view('livewire.dynamic-entry');
    }
}
