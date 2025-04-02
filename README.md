# Filament Forms

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tapp/filament-form-builder.svg?style=flat-square)](https://packagist.org/packages/tapp/filament-form-builder)
![GitHub Tests Action Status](https://github.com/TappNetwork/Filament-Form-Builder/actions/workflows/run-tests.yml/badge.svg)
![GitHub Code Style Action Status](https://github.com/TappNetwork/Filament-Form-Builder/actions/workflows/fix-php-code-style-issues.yml/badge.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/tapp/filament-form-builder.svg?style=flat-square)](https://packagist.org/packages/tapp/filament-form-builder)

A Filament plugin and package that allows the creation of forms via the admin panel for collecting user data on the front end. Forms are composed of filament field components and support all Laravel validation rules. Form responses can be rendered on the front end of exported to .csv.

## Requirements

-   PHP 8.2+
-   Laravel 11.0+
-   [Filament 3.0+](https://github.com/laravel-filament/filament)

## Dependencies

-   [maatwebsite/excel](https://github.com/SpartnerNL/Laravel-Excel)
-   [spatie/eloquent-sortable](https://github.com/spatie/eloquent-sortable)

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
            FilamentFormBuilderPlugin::make(),
            //...
        ]);
}
```

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

### Events
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

## Handling Form Deletion with Custom Relations

If your project has custom relations to the `FilamentForm` model (like tests, surveys, etc.), you can handle their deletion by listening to the `FilamentFormForceDeletingEvent` event. This event is fired before a form is force deleted.

```php
namespace App\Listeners;

use Tapp\FilamentFormBuilder\Events\FilamentFormForceDeletingEvent;

class HandleCustomFormDeletion
{
    public function handle(FilamentFormForceDeletingEvent $event): void
    {
        // Access the form being deleted
        $form = $event->form;

        // Delete your custom relations
        $form->tests()->delete();
        $form->surveys()->delete();
        // ... etc
    }
}
```

Register the listener in your `EventServiceProvider`:

```php
protected $listen = [
    \Tapp\FilamentFormBuilder\Events\FilamentFormForceDeletingEvent::class => [
        \App\Listeners\HandleCustomFormDeletion::class,
    ],
];
```

This way, when a form is force deleted:
1. The package will fire the `FilamentFormForceDeletingEvent`
2. Your listener will handle the deletion of custom relations
3. The package will then delete its own relations (form fields and form users)
4. Finally, the form itself will be deleted

This approach keeps the package clean and project-agnostic while allowing you to handle custom relations in your project.
