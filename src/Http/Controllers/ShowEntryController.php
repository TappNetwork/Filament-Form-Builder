<?php

declare(strict_types=1);

namespace Tapp\FilamentFormBuilder\Http\Controllers;

use Illuminate\Http\Request;
use Livewire\Livewire;
use Tapp\FilamentFormBuilder\Filament\Pages\ShowEntry;
use Tapp\FilamentFormBuilder\Models\FilamentFormUser;

class ShowEntryController
{
    public function __invoke(Request $request, FilamentFormUser $entry)
    {
        return Livewire::mount(ShowEntry::class, ['entry' => $entry]);
    }
}
