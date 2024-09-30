# Changelog

All notable changes to `:package_name` will be documented in this file.

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