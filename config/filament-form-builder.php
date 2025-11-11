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

    // Multi-Tenancy configuration
    'tenancy' => [
        // Enable tenancy support
        'enabled' => false,

        // The Tenant model class (e.g., App\Models\Team::class, App\Models\Organization::class)
        'model' => null,

        // The tenant relationship name (defaults to snake_case of tenant model class name)
        // For example: Team::class -> 'team', Organization::class -> 'organization'
        // This should match what you configure in your Filament Panel:
        // ->tenantOwnershipRelationshipName('team')
        'relationship_name' => null,

        // The tenant column name (defaults to snake_case of tenant model class name + '_id')
        // You can override this if needed
        'column' => null,
    ],
];
