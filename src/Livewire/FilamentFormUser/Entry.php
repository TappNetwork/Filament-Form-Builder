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
<<<<<<< HEAD
        return view('filament-form-builder::livewire.filament-form-user.entry');
=======
        /** @var view-string */
        $view = 'filament-form-builder::livewire.filament-form-user.entry';

        return view($view);
>>>>>>> a4c3661a25cfdf309dca797e8406689bc2e88560
    }
}
