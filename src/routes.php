<?php

use Illuminate\Support\Facades\Route;
use Tapp\FilamentFormBuilder\Livewire\FilamentForm\Show as FilamentFormShow;
use Tapp\FilamentFormBuilder\Livewire\FilamentFormUser\Show as FilamentFormUserShow;

Route::get('filament-form-users/{entry}', FilamentFormUserShow::class)
    ->middleware('web')
    ->name('filament-form-users.show');

Route::get('filament-forms/{form}', FilamentFormShow::class)
    ->middleware('web')
    ->name('filament-forms.show');
