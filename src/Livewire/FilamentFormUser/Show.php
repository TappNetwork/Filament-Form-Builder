<?php

declare(strict_types=1);

namespace Tapp\FilamentFormBuilder\Livewire\FilamentFormUser;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Schemas\Schema;
use Livewire\Component;
use Tapp\FilamentFormBuilder\Filament\Infolists\FilamentFormUserEntryInfolist;
use Tapp\FilamentFormBuilder\Models\FilamentFormUser;

class Show extends Component implements HasActions, HasForms, HasInfolists
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithInfolists;

    public FilamentFormUser $entry;

    public bool $compact = false;

    public function mount(FilamentFormUser $entry, bool $compact = false): void
    {
        $this->compact = $compact;
        $this->entry = $entry->load('user', 'filamentForm');
    }

    public function entryInfoList(Schema $schema): Schema
    {
        return FilamentFormUserEntryInfolist::configure(
            $schema->record($this->entry),
            dense: $this->compact,
        );
    }

    public function render()
    {
        /** @phpstan-ignore-next-line */
        return view('filament-form-builder::livewire.filament-form-user.show');
    }
}
