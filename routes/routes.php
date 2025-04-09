<?php

use Illuminate\Support\Facades\Route;
use Tapp\FilamentFormBuilder\Livewire\FilamentForm\Form as FilamentForm;
use Tapp\FilamentFormBuilder\Livewire\FilamentFormUser\Entry as FilamentFormUserEntry;

Route::get(config('filament-form-builder.filament-form-user-uri').'/{entry}', FilamentFormUserEntry::class)
    ->middleware('web')
    ->name('filament-form-users.show');

Route::get(config('filament-form-builder.filament-form-uri').'/{form}', FilamentForm::class)
    ->middleware('web')
    ->name('filament-form-builder.show');
