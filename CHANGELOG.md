# Changelog

All notable changes to `Filament-Form-Builder` will be documented in this file.

## v4.0.8 - 2026-01-07

### What's Changed

* Multi-tenancy support by @andreia in https://github.com/TappNetwork/Filament-Form-Builder/pull/37

**Full Changelog**: https://github.com/TappNetwork/Filament-Form-Builder/compare/v4.0.6...v4.0.8

## v1.51 - 2025-07-08

**Full Changelog**: https://github.com/TappNetwork/Filament-Form-Builder/compare/v1.43...v1.51

## v1.43 - 2025-07-08

### What's Changed

* use the filament button component by @swilla in https://github.com/TappNetwork/Filament-Form-Builder/pull/21

**Full Changelog**: https://github.com/TappNetwork/Filament-Form-Builder/compare/v1.5.0...v1.43

## v1.5.0 - 2025-06-24

### What's Changed

* Repeater and Heading fields by @scottgrayson in https://github.com/TappNetwork/Filament-Form-Builder/pull/15

**Full Changelog**: https://github.com/TappNetwork/Filament-Form-Builder/compare/v1.42...v1.5.0

## v1.42 - 2025-05-29

### What's Changed

* Laravel 12 Support by @swilla in https://github.com/TappNetwork/Filament-Form-Builder/pull/6
* Bump dependabot/fetch-metadata from 1.6.0 to 2.3.0 by @dependabot in https://github.com/TappNetwork/Filament-Form-Builder/pull/7
* Bump aglipanci/laravel-pint-action from 2.4 to 2.5 by @dependabot in https://github.com/TappNetwork/Filament-Form-Builder/pull/8
* add event and layouts for public forms by @scottgrayson in https://github.com/TappNetwork/Filament-Form-Builder/pull/10
* Can we add a preview cu 868cwr2en by @johnwesely in https://github.com/TappNetwork/Filament-Form-Builder/pull/11
* File Uploads by @scottgrayson in https://github.com/TappNetwork/Filament-Form-Builder/pull/12
* Bump dependabot/fetch-metadata from 2.3.0 to 2.4.0 by @dependabot in https://github.com/TappNetwork/Filament-Form-Builder/pull/13
* Middleware for form show that handles guest entries by @scottgrayson in https://github.com/TappNetwork/Filament-Form-Builder/pull/14

### New Contributors

* @dependabot made their first contribution in https://github.com/TappNetwork/Filament-Form-Builder/pull/7
* @scottgrayson made their first contribution in https://github.com/TappNetwork/Filament-Form-Builder/pull/10

**Full Changelog**: https://github.com/TappNetwork/Filament-Form-Builder/compare/v1.41...v1.42

## v.41 - 2025-01-22

Fix bug with single select fields when multi select fields were introduced.

## v1.4.0 - 2025-01-14

Adds support for Select()->multiple() fields.

## v1.31 - 2024-09-30

Actually commit changes described in last release

## v1.30 - 2024-09-30

Fixes a bug with copy action

## v1.29 - 2024-09-30

This release add a copy action for filament forms.

## v1.28 - 2024-08-26

Makes type field required when creating a field.

## v1.27 - 2024-08-21

Add 'fb-form-user-container' class for styling form results container.

## v1.26 - 2024-08-12

Sends id of saved entry instead of entry itself when entrySaved event is dispatched to resolve intermittent error with laravel model biding on event consumption.

## v1.25 - 2024-08-09

This Update fixes a bug when the first option was selected in a radio select.

## v1.24 - 2024-08-08

This PR adds a $blockRedirect property to the FilamentForm/Show component. Passing this as true to the component will prevent the redirect action when a new form entry is saved.

## v1.23 - 2024-08-06

Add styles to plugin

## v1.22 - 2024-08-06

Include stylesheet for filament classes

## v1.21 - 2024-08-05

Fixes a typo in locking action visibility and adds a locked column to form resource.

## v1.2 - 2024-08-05

### Support locking a form to prevent data integrity issues

1.2 supports locking forms so that entries from a form can always be compared apples to apples over time with no risk of the form being changed and previous entries becoming incompatible with new entires. If you are upgrading from 1.0 or 1.1 to 1.2, create a migration with the following method to reflect this change

```
    Schema::table('filament_forms', function (Blueprint $table) {
        $table->boolean('locked')->default(false);
    });



















```