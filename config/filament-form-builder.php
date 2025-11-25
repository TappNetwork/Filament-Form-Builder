<?php

use Tapp\FilamentFormBuilder\Filament\Resources\FilamentFormResource;

return [
    'filament-form-user-show-route' => 'filament-form-users.show',

    'filament-form-show-route' => 'filament-form-builder.show',

    'filament-form-user-uri' => 'entries',

    'filament-form-uri' => 'forms',

    'resources' => [
        'FilamentFormResource' => FilamentFormResource::class,
    ],

    'admin-panel-resource-name' => 'Form',

    'admin-panel-resource-name-plural' => 'Forms',

    'admin-panel-group-name' => 'Forms',

    'admin-panel-icon' => 'heroicon-o-clipboard-document-list',

    'admin-panel-filament-form-user-name' => 'Entry',

    'admin-panel-filament-form-user-name-plural' => 'Entries',

    'admin-panel-filament-form-field-name' => 'Field',

    'admin-panel-filament-form-field-name-plural' => 'Fields',

    'preview-route' => 'filament-form-builder.show',

    /*
    |--------------------------------------------------------------------------
    | Notification Emails Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the notification emails field in the form builder.
    |
    | 'user_model': The User model class to use for the select field.
    |               Set to null to use TagsInput for manual email entry.
    |               Default: 'App\Models\User'
    |
    | Example: 'user_model' => null, // Use TagsInput instead
    |
    */
    'user_model' => 'App\Models\User',
];
