<?php

use Tapp\FilamentFormBuilder\Filament\Pages\ShowEntry;
use Tapp\FilamentFormBuilder\Filament\Pages\ShowForm;
use Tapp\FilamentFormBuilder\Filament\Resources\FilamentFormResource;
use Tapp\FilamentFormBuilder\Http\Middleware\SetFormPanel;

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
     * Panel IDs Configuration
     * Configure the panel IDs used for guest and app panels.
     */
    'guest-panel-id' => 'guest',
    'app-panel-id' => 'app',

    /*
     * Admin Panel Configuration
     * When set, authenticated users who can access this panel and arrive from an admin URL
     * (referer contains the admin path) will use it for public form/entry routes instead of the app panel.
     * Example: 'admin-panel-id' => 'admin',
     */
    'admin-panel-id' => null,

    /*
     * When true, any authenticated user who can access the admin panel will use it for form/entry routes,
     * not only when the referer is an admin URL.
     */
    'prefer-admin-panel-for-authenticated-form-routes' => false,

    /*
     * How entry rows open from the admin panel Entries relation manager.
     * - slideover: stay on the form edit page and open a slide-over (recommended for admin).
     * - page: navigate to the public /entries/{id} route (for app/guest preview links).
     */
    'admin-panel-entry-display' => 'slideover',

    /*
     * Login Route Configuration
     * The route name for the login page. Used when redirecting unauthenticated users.
     */
    'login-route' => 'filament.app.auth.login',

    /*
     * Guest Panel Configuration
     * The page class to use for displaying forms in the guest panel.
     * Set to null to use the package default, or provide your own custom page class.
     * Example: \App\Filament\Guest\Pages\ShowForm::class
     */
    'guest-panel-form-page-class' => ShowForm::class,

    /*
     * App Panel Configuration
     * The page class to use for displaying forms in the app panel.
     * Set to null to use the package default, or provide your own custom page class.
     * Example: \App\Filament\App\Pages\ShowForm::class
     */
    'app-panel-form-page-class' => ShowForm::class,

    /*
     * Set Form Panel Middleware Configuration
     * The middleware class to use for setting the panel context based on authentication.
     * Set to null to use the package default, or provide your own custom middleware class.
     * Example: \App\Http\Middleware\SetFormPanel::class
     */
    'set-form-panel-middleware-class' => SetFormPanel::class,

    /*
     * Guest Panel Entry Configuration
     * The page class to use for displaying form entries in the guest panel.
     * Set to null to use the package default, or provide your own custom page class.
     * Example: \App\Filament\Guest\Pages\ShowEntry::class
     */
    'guest-panel-entry-page-class' => ShowEntry::class,

    /*
     * App Panel Entry Configuration
     * The page class to use for displaying form entries in the app panel.
     * Set to null to use the package default, or provide your own custom page class.
     * Example: \App\Filament\App\Pages\ShowEntry::class
     */
    'app-panel-entry-page-class' => ShowEntry::class,

    /*
     |--------------------------------------------------------------------------
     | Tenancy Configuration
     |--------------------------------------------------------------------------
     |
     | Configure multi-tenancy settings.
     |
     */
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
