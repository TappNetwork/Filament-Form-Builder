<?php

use Illuminate\Support\Facades\Route;
use Tapp\FilamentFormBuilder\Livewire\FilamentForm\Show as FilamentFormShow;
use Tapp\FilamentFormBuilder\Livewire\FilamentFormUser\Show as FilamentFormUserShow;

Route::get(config('filament-form-builder.filament-form-user-uri') . '/{entry}', FilamentFormUserShow::class)
    ->middleware('web')
    ->name('filament-form-users.show');

Route::get(config('filament-form-builder.filament-form-uri') . '/{form}', FilamentFormShow::class)
    ->middleware('web')
    ->name('filament-form-builder.show');
