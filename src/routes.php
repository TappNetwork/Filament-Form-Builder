<?php

use Illuminate\Support\Facades\Route;
use Tapp\FilamentForms\Livewire\FilamentForm\Show as FilamentFormShow;
use Tapp\FilamentForms\Livewire\FilamentFormUser\Show as FilamentFormUserShow;

Route::get('filament-form-users/{entry}', FilamentFormUserShow::class)->name('filament-form-users.show');
Route::get('filament-forms/{form}', FilamentFormShow::class)->name('filament-forms.show');
