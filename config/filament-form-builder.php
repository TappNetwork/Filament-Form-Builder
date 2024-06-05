<?php

use Tapp\FilamentFormBuilder\Filament\Resources\FilamentFormResource;

return [
    'filament-form-user-show-route' => 'filament-form-users.show',

    'filament-form-show-route' => 'filament-form-builder.show',

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
];
