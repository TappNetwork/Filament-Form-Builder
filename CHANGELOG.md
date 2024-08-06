# Changelog

All notable changes to `:package_name` will be documented in this file.

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