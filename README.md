# Filament Forms

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tapp/filament-form-builder.svg?style=flat-square)](https://packagist.org/packages/tapp/filament-form-builder)
![GitHub Tests Action Status](https://github.com/TappNetwork/Filament-Form-Builder/actions/workflows/run-tests.yml/badge.svg)
![GitHub Code Style Action Status](https://github.com/TappNetwork/Filament-Form-Builder/actions/workflows/fix-php-code-style-issues.yml/badge.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/tapp/filament-form-builder.svg?style=flat-square)](https://packagist.org/packages/tapp/filament-form-builder)

A Filament plugin and package that allows the creation of forms via the admin panel for collecting user data on the front end. Forms are composed of filament field components and support all Laravel validation rules. Form responses can be rendered on the front end or exported to .csv.

## Requirements

-   PHP 8.2+
-   Laravel 11.0+
-   [Filament 4.x / 5.x](https://github.com/laravel-filament/filament)

## Dependencies

-   [maatwebsite/excel](https://github.com/SpartnerNL/Laravel-Excel)
-   [spatie/eloquent-sortable](https://github.com/spatie/eloquent-sortable)

## Version Compatibility

Filament | Filament Form Builder | Documentation
:--------|:-------------------|:--------------
4.x/5.x  | 4.x                | Current
3.x      | 1.x                | [Check the docs](https://github.com/TappNetwork/Filament-Form-Builder/tree/1.x)

### Installing the Filament Forms Package

Install the plugin via Composer:

This package is not yet on Packagist. Add the repository to your composer.json

```json
{
"repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/TappNetwork/Filament-Form-Builder"
        }
    ],
}
```

```bash
composer require tapp/filament-form-builder:"^4.0"
```

You can publish the migrations with:

```bash
php artisan vendor:publish --tag="filament-form-builder-migrations"
```

> [!WARNING]  
> If you are using multi-tenancy please see the "Multi-Tenancy Support" instructions below **before** publishing and running migrations.

You can run the migrations with:

```bash
php artisan migrate
```

#### Optional: Publish the package's views, translations, and config

You can publish the view file with:

```bash
php artisan vendor:publish --tag="filament-form-builder-views"
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-form-builder-config"
```

### Adding the plugins to panels

The package provides three plugins for different panel types:

#### 1. Admin Panel Plugin (`FilamentFormBuilderPlugin`)

Add this plugin to your **admin panel** to manage forms, fields, and entries. This plugin registers the `FilamentFormResource` which provides CRUD operations for forms.

```php
use Tapp\FilamentFormBuilder\FilamentFormBuilderPlugin;

// In app/Providers/Filament/AdminPanelProvider.php
public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugins([
            FilamentFormBuilderPlugin::make(),
            //...
        ]);
}
```

#### 2. Guest Panel Plugin (`FilamentFormBuilderGuestPlugin`)

Add this plugin to your **guest panel** (for unauthenticated users) to display forms and form entries. This plugin registers the `ShowForm` and `ShowEntry` pages for public access.

```php
use Tapp\FilamentFormBuilder\FilamentFormBuilderGuestPlugin;

// In app/Providers/Filament/GuestPanelProvider.php
public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugins([
            FilamentFormBuilderGuestPlugin::make(),
            //...
        ]);
}
```

#### 3. Frontend/App Panel Plugin (`FilamentFormBuilderFrontendPlugin`)

Add this plugin to your **app/frontend panel** (for authenticated users) to display forms and form entries. This plugin registers the `ShowForm` and `ShowEntry` pages for authenticated access.

```php
use Tapp\FilamentFormBuilder\FilamentFormBuilderFrontendPlugin;

// In app/Providers/Filament/AppPanelProvider.php
public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugins([
            FilamentFormBuilderFrontendPlugin::make(),
            //...
        ]);
}
```

### Public Form Access

Forms can be accessed via the following routes (configured in `config/filament-form-builder.php`):

- **Form View**: `/forms/{form}` - Displays a form for submission
- **Entry View**: `/entries/{entry}` - Displays a submitted form entry

The routes automatically use the appropriate panel (guest or app) based on authentication status. Forms with `permit_guest_entries` enabled can be viewed by unauthenticated users in the guest panel, while authenticated users will be redirected to the app panel.

### Configuration

You can customize the package behavior by publishing and editing the config file:

```bash
php artisan vendor:publish --tag="filament-form-builder-config"
```

Key configuration options include:

- **Panel IDs**: Configure which panel IDs are used for guest and app panels (`guest-panel-id`, `app-panel-id`)
- **Login Route**: Set the login route for redirecting unauthenticated users (`login-route`)
- **Custom Page Classes**: Override the default `ShowForm` and `ShowEntry` pages for guest and app panels
- **Custom Middleware**: Override the default `SetFormPanel` middleware for panel context switching
- **Route URIs**: Customize the form and entry route paths (`filament-form-uri`, `filament-form-user-uri`)

See `config/filament-form-builder.php` for all available configuration options.

### Configuring Tailwind:

Add this to your tailwind.config.js content section:

```
    content: [
        ...
        "./vendor/tapp/**/*.blade.php",
    ],
```

### Disabling Redirect
You can disable the redirect when including the Form/Show component inside of another component by passing the 'blockRedirect' prop as follows
```
    @livewire('tapp.filament-form-builder.livewire.filament-form.show', ['form' => $test->form, 'blockRedirect' => true])
```

## Multi-Tenancy Support

This plugin includes comprehensive support for multi-tenancy, allowing you to scope forms, form fields, and entries to specific tenants (e.g., Teams, Organizations, Companies).

### ⚠️ Important: Configure Before Migration

**You MUST enable and configure tenancy BEFORE running migrations!** The migrations check the tenancy configuration to determine whether to add tenant columns to the database tables. Enabling tenancy after running migrations will require manual database modifications.

### Configuration

Update your `config/filament-form-builder.php` configuration file:

```php
'tenancy' => [
    // Enable tenancy support
    'enabled' => true,

    // The Tenant model class
    'model' => \App\Models\Team::class,

    // Optional: Override the tenant relationship name
    // (defaults to snake_case of tenant model class name: Team -> 'team')
    'relationship_name' => null,

    // Optional: Override the tenant foreign key column name
    // (defaults to relationship_name + '_id': 'team' -> 'team_id')
    'column' => null,
],
```

### Setup Steps

1. **Configure tenancy** in `config/filament-form-builder.php` (set `enabled` to `true` and specify your tenant model)
2. **Publish migrations**: `php artisan vendor:publish --tag="filament-form-builder-migrations"`
3. **Run migrations**: `php artisan migrate`
4. **Configure your Filament Panel** with tenancy:

```php
use Filament\Panel;
use App\Models\Team;
use Tapp\FilamentFormBuilder\FilamentFormBuilderPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->tenant(Team::class)
        ->plugins([
            FilamentFormBuilderPlugin::make(),
        ]);
}
```

### How It Works

When tenancy is enabled:

- **Automatic Scoping**: All queries within Filament panels are automatically scoped to the current tenant
- **URL Structure**: Forms are accessed via tenant-specific URLs: `/admin/{tenant-slug}/filament-forms`
- **Data Isolation**: Each tenant can only access their own forms, fields, and entries
- **Cascade Deletion**: Deleting a tenant automatically removes all associated form data

### Disabling Tenancy

To disable tenancy, set `enabled` to `false` in your configuration:

```php
'tenancy' => [
    'enabled' => false,
    'model' => null,
],
```

## Events

### Livewire
The FilamentForm/Show component emits an 'entrySaved' event when a form entry is saved. You can handle this event in a parent component to as follows.
```
class ParentComponent extends Component
{
    protected $listeners = ['entrySaved'];

    public function entrySaved(FilamentFormUser $survey)
    {
        // custom logic you would like to add to form entry saving logic
    }
}

```

### Laravel
The component also emits a Laravel event that you can listen to in your event service provider
```php
// In your EventServiceProvider.php
protected $listen = [
    \Tapp\FilamentFormBuilder\Events\EntrySaved::class => [
        \App\Listeners\HandleFormSubmission::class,
    ],
];

// Create a listener class
namespace App\Listeners;

use Tapp\FilamentFormBuilder\Events\EntrySaved;

class HandleFormSubmission
{
    public function handle(EntrySaved $event): void
    {
        // Access the form entry
        $entry = $event->entry;
        
        // Perform actions with the form data
        // For example, send notifications, update other records, etc.
    }
}
```
