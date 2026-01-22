<?php

declare(strict_types=1);

namespace Tapp\FilamentFormBuilder\Http\Controllers;

use Illuminate\Http\Request;
use Livewire\Livewire;
use Tapp\FilamentFormBuilder\Filament\Pages\ShowForm;
use Tapp\FilamentFormBuilder\Models\FilamentForm;

class ShowFormController
{
    public function __invoke(Request $request, FilamentForm $form)
    {
        return Livewire::mount(ShowForm::class, ['form' => $form]);
    }
}
