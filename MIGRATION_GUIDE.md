# Migration Guide: Moving Form Builder Code to Package

This document explains what code was moved from the project to the package, and what must remain in the project.

## Code Moved to Package

The following generic, reusable code has been moved to the `filament-form-builder` package:

### 1. Filament Pages
- **`Tapp\FilamentFormBuilder\Filament\Pages\ShowForm`** - Generic page for displaying forms in both guest and app panels
- **`Tapp\FilamentFormBuilder\Filament\Pages\ShowEntry`** - Generic page for displaying form entries in both guest and app panels

These pages handle:
- Dynamic panel switching based on authentication
- Guest entry permission checks
- Redirects to login when needed
- Security checks for entry viewing (signed URLs for guests, ownership checks for authenticated users)

### 2. Middleware
- **`Tapp\FilamentFormBuilder\Http\Middleware\SetFormPanel`** - Generic middleware that dynamically sets the Filament panel based on authentication status

### 3. Blade Views
- **`filament-form-builder::pages.show-form-guest`** - Guest panel form view
- **`filament-form-builder::pages.show-form-app`** - App panel form view
- **`filament-form-builder::pages.show-entry-guest`** - Guest panel entry view
- **`filament-form-builder::pages.show-entry-app`** - App panel entry view

### 4. Plugin Updates
- **`FilamentFormBuilderGuestPlugin`** - Now automatically registers default pages for guest panel
- **`FilamentFormBuilderFrontendPlugin`** - Now automatically registers default pages for app panel

### 5. Service Provider Updates
- **`FilamentFormBuilderServiceProvider`** - Now uses package defaults if config values are null

## Configuration Changes

The package now provides sensible defaults for all page classes and middleware. Projects can override these via config if needed:

```php
// config/filament-form-builder.php

// Panel IDs - MUST be configured to match your Filament panel IDs
'guest-panel-id' => 'guest',
'app-panel-id' => 'app',

// Login route - MUST be configured to match your login route
'login-route' => 'filament.app.auth.login',

// Page classes - Set to null to use package defaults, or provide custom classes
'guest-panel-form-page-class' => null,  // Uses package default
'app-panel-form-page-class' => null,    // Uses package default
'guest-panel-entry-page-class' => null, // Uses package default
'app-panel-entry-page-class' => null,   // Uses package default

// Middleware - Set to null to use package default
'set-form-panel-middleware-class' => null, // Uses package default
```

## Code That MUST Stay in Project

The following code is project-specific and cannot be moved to the package:

### 1. Panel Providers
**Location:** `app/Providers/Filament/GuestPanelProvider.php` and `app/Providers/Filament/AppPanelProvider.php`

**Why:** Each project has different:
- Panel IDs (e.g., 'guest', 'app', 'admin')
- Panel paths (e.g., '/', '/admin')
- Branding (logos, fonts, colors)
- Navigation items
- Middleware stacks
- User menu items
- Render hooks

**What Changed:**
- Removed explicit page registrations (now handled by plugins)
- Plugins are still registered: `FilamentFormBuilderGuestPlugin::make()` and `FilamentFormBuilderFrontendPlugin::make()`

### 2. Project-Specific Config Values
**Location:** `config/filament-form-builder.php`

**Why:** Each project has different:
- Panel IDs
- Login route names
- Custom page classes (if overriding package defaults)
- Custom middleware (if overriding package defaults)

**What Changed:**
- Config values for page classes and middleware are now set to `null` to use package defaults
- Panel IDs and login route must still be configured per project

### 3. Optional: Custom Page Classes (if needed)
**Location:** `app/Filament/Guest/Pages/` and `app/Filament/App/Pages/`

**Why:** Projects may need custom logic that extends beyond what the package provides.

**When to Override:**
- Custom authorization logic
- Custom redirect behavior
- Custom view rendering
- Integration with project-specific features

**How to Override:**
1. Create custom page classes extending the package pages or `Filament\Pages\Page`
2. Set the config value to your custom class:
   ```php
   'guest-panel-form-page-class' => \App\Filament\Guest\Pages\ShowForm::class,
   ```

## Migration Steps for New Projects

1. **Install the package** (already done if reading this)

2. **Publish and configure the config file:**
   ```bash
   php artisan vendor:publish --tag=filament-form-builder-config
   ```

3. **Update `config/filament-form-builder.php`:**
   - Set `guest-panel-id` to match your guest panel ID
   - Set `app-panel-id` to match your app panel ID
   - Set `login-route` to match your login route name
   - Leave page class and middleware configs as `null` to use package defaults

4. **Register plugins in your panel providers:**
   ```php
   // In GuestPanelProvider
   ->plugins([
       FilamentFormBuilderGuestPlugin::make(),
       // ... other plugins
   ])

   // In AppPanelProvider
   ->plugins([
       FilamentFormBuilderFrontendPlugin::make(),
       // ... other plugins
   ])
   ```

5. **That's it!** The package handles everything else automatically.

## Removing Project-Specific Code (Optional)

If you had previously created project-specific page classes and middleware, you can now remove them:

- `app/Filament/Guest/Pages/ShowForm.php` - Can be deleted (using package default)
- `app/Filament/App/Pages/ShowForm.php` - Can be deleted (using package default)
- `app/Filament/Guest/Pages/ShowEntry.php` - Can be deleted (using package default)
- `app/Filament/App/Pages/ShowEntry.php` - Can be deleted (using package default)
- `app/Http/Middleware/SetFormPanel.php` - Can be deleted (using package default)
- `resources/views/filament/guest/pages/show-form.blade.php` - Can be deleted (using package default)
- `resources/views/filament/app/pages/show-form.blade.php` - Can be deleted (using package default)
- `resources/views/filament/guest/pages/show-entry.blade.php` - Can be deleted (using package default)
- `resources/views/filament/app/pages/show-entry.blade.php` - Can be deleted (using package default)

**Note:** Only delete these if you're not overriding any functionality. If you need custom behavior, keep your custom classes and reference them in the config.
