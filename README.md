# Filament Forms

A Filament plugin and and package that allows the creation of forms via the admin panel for collecting user data on the front end. Forms are composed of filament field components and support all Laravel validation rules. Form responses can be rendered on the front end of exported to .csv.

## Requirements
- PHP 8.2+
- Laravel 11.0+
- [Filament 3.0+](https://github.com/laravel-filament/filament)

## Dependencies
- [maatwebsite/excel](https://github.com/SpartnerNL/Laravel-Excel)
- [spatie/eloquent-sortable](https://github.com/spatie/eloquent-sortable)

### Installing the Filament Forms Package

Install the plugin via Composer:

```bash
composer require tapp/filament-form-builder
```

public and run migrations with

```bash
php artisan vendor:publish --tag="filament-form-builder-migrations"
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

### Adding the plugin to a panel

Add this plugin to a panel on `plugins()` method (e.g. in `app/Providers/Filament/AdminPanelProvider.php`).

```php
use Tapp\FilamentFormBuilder\FilamentFormBuilderPlugin;
 
public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugins([
            FilamentFormsPlugin::make(),
            //...
        ]);
}
```
