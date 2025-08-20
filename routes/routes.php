<?php

use Illuminate\Support\Facades\Route;
use Tapp\FilamentFormBuilder\Livewire\FilamentForm\Form as FilamentForm;
use Tapp\FilamentFormBuilder\Livewire\FilamentFormUser\Entry as FilamentFormUserEntry;
use Tapp\FilamentFormBuilder\Middleware\CheckFormGuestAccess;

Route::get(config('filament-form-builder.filament-form-user-uri').'/{entry}', FilamentFormUserEntry::class)
    ->middleware('auth')
    ->name('filament-form-users.show');

Route::get(config('filament-form-builder.filament-form-uri').'/{form}', FilamentForm::class)
    ->middleware(['web', CheckFormGuestAccess::class])
    ->name('filament-form-builder.show');
