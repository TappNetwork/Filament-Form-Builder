<?php

namespace Tapp\FilamentFormBuilder\Livewire\FilamentFormUser;

use Livewire\Component;
use Tapp\FilamentFormBuilder\Models\FilamentFormUser;

class Entry extends Component
{
    public FilamentFormUser $entry;

    public function mount(FilamentFormUser $entry)
    {
        $this->entry = $entry;
    }

    public function render()
    {
        /** @phpstan-ignore-next-line */
        return view('filament-form-builder::livewire.filament-form-user.entry');
    }
}
