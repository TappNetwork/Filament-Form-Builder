<?php

use Tapp\FilamentForms\Filament\Resources\FilamentFormResource;

return [
    'filament-form-user-show-route' => 'filament-form-users.show',

    'resources' => [
        'FilamentFormResource' => FilamentFormResource::class,
    ],

    'admin-panel-resource-name' => 'Form',
];
