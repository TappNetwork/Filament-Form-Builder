<?php

namespace Tapp\FilamentFormBuilder\Livewire\FilamentForm;

use Livewire\Component;
use Tapp\FilamentFormBuilder\Models\FilamentForm;

class Form extends Component
{
    public FilamentForm $form;

    public function mount(FilamentForm $form)
    {
        $this->form = $form;
    }

    public function render()
    {
        /** @phpstan-ignore-next-line */
        return view('filament-form-builder::livewire.filament-form.form');
    }
}
