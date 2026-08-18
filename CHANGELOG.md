# Changelog

All notable changes to `Filament-Form-Builder` will be documented in this file.

## v4.3.5 - 2026-07-03

### What's Changed

* Update npm lockfile for glob advisory by @swilla in https://github.com/TappNetwork/Filament-Form-Builder/pull/78
* Bump actions/checkout from 6 to 7 by @dependabot[bot] in https://github.com/TappNetwork/Filament-Form-Builder/pull/79
* Allow multiple form submissions by @andreia in https://github.com/TappNetwork/Filament-Form-Builder/pull/80

**Full Changelog**: https://github.com/TappNetwork/Filament-Form-Builder/compare/v4.3.4...v4.3.5

## v4.3.4 - 2026-05-28

### What's Changed

* Add config for admin panel and fix tenancy for entry by @andreia in https://github.com/TappNetwork/Filament-Form-Builder/pull/77

**Full Changelog**: https://github.com/TappNetwork/Filament-Form-Builder/compare/v4.3.3...v4.3.4

## v4.3.3 - 2026-04-28

### What's Changed

* Bump maatwebsite/excel to ^4.0 on 4.x by @swilla in https://github.com/TappNetwork/Filament-Form-Builder/pull/75
* Update maatwebsite/excel version constraint by @swilla in https://github.com/TappNetwork/Filament-Form-Builder/pull/76

**Full Changelog**: https://github.com/TappNetwork/Filament-Form-Builder/compare/v4.3.2...v4.3.3

## v4.3.2 - 2026-04-27

### What's Changed

* Bump dependabot/fetch-metadata from 3.0.0 to 3.1.0 by @dependabot[bot] in https://github.com/TappNetwork/Filament-Form-Builder/pull/72
* Bump postcss from 8.4.41 to 8.5.10 by @dependabot[bot] in https://github.com/TappNetwork/Filament-Form-Builder/pull/73
* Drop PHP 8.2, add PHP 8.5 to CI matrix by @swilla in https://github.com/TappNetwork/Filament-Form-Builder/pull/74

**Full Changelog**: https://github.com/TappNetwork/Filament-Form-Builder/compare/v4.3.1...v4.3.2

## v4.3.1 - 2026-04-22

### What's Changed

* Fix RichEditor toolbar actions (e.g. link) crashing in Show component by adding `HasActions` / `InteractsWithActions`

**Full Changelog**: https://github.com/TappNetwork/Filament-Form-Builder/compare/v4.3.0...v4.3.1

## v4.3.0 - 2026-04-14

### What's Changed

* Add Laravel 13 support by @swilla in https://github.com/TappNetwork/Filament-Form-Builder/pull/69
* Add Laravel 13 support by @swilla in https://github.com/TappNetwork/Filament-Form-Builder/pull/70

**Full Changelog**: https://github.com/TappNetwork/Filament-Form-Builder/compare/v4.2.0...v4.3.0

## v4.2.0 - 2026-04-10

### What's Changed

- Fix mobile overflow: remove `min-w` constraints that caused horizontal overflow on mobile viewports
- Reduce opinionated styling: remove forced borders, backgrounds, shadows, and border-radius from form views
- Apps can now fully customize form appearance via `.fb-form-container`, `.fb-form-component`, and `.fb-form-user-container` CSS hooks

## v4.1.8 - 2026-03-03

**Full Changelog**: https://github.com/TappNetwork/Filament-Form-Builder/compare/v4.1.7...v4.1.8

## v4.1.7 - 2026-03-03

### What's Changed

* Use policy for viewing form entries when app registers one by @scottgrayson in https://github.com/TappNetwork/Filament-Form-Builder/pull/63

**Full Changelog**: https://github.com/TappNetwork/Filament-Form-Builder/compare/v4.1.6...v4.1.7

## v4.1.6 - 2026-03-02

### What's Changed

* Allow form description to be rich text / HTML by @scottgrayson in https://github.com/TappNetwork/Filament-Form-Builder/pull/62

**Full Changelog**: https://github.com/TappNetwork/Filament-Form-Builder/compare/v4.1.5...v4.1.6

## v4.1.5 - 2026-03-02

### What's Changed

* Bump minimatch from 9.0.5 to 9.0.9 by @dependabot[bot] in https://github.com/TappNetwork/Filament-Form-Builder/pull/61
* Add private entries support for form entry visibility by @scottgrayson in https://github.com/TappNetwork/Filament-Form-Builder/pull/59

**Full Changelog**: https://github.com/TappNetwork/Filament-Form-Builder/compare/v4.1.4...v4.1.5

## v4.1.4 - 2026-02-20

removes file upload from the rich text editor as this is not supported functionality and throws an exception.

## v4.1.3 - 2026-02-13

### What's Changed

* Table actions in ActionGroup at row start by @scottgrayson in https://github.com/TappNetwork/Filament-Form-Builder/pull/56

**Full Changelog**: https://github.com/TappNetwork/Filament-Form-Builder/compare/v4.1.2...v4.1.3

## v4.1.2 - 2026-02-06

### What's Changed

* Restrict copy action to users who can create forms by @scottgrayson in https://github.com/TappNetwork/Filament-Form-Builder/pull/55

**Full Changelog**: https://github.com/TappNetwork/Filament-Form-Builder/compare/v4.1.1...v4.1.2

## v4.1.1 - 2026-01-28

### What's Changed

* Change field label to text by @andreia in https://github.com/TappNetwork/Filament-Form-Builder/pull/54

**Full Changelog**: https://github.com/TappNetwork/Filament-Form-Builder/compare/v4.1.0...v4.1.1

## v4.1.0 - 2026-01-23

### What's Changed

* Bump aglipanci/laravel-pint-action from 2.5 to 2.6 by @dependabot[bot] in https://github.com/TappNetwork/Filament-Form-Builder/pull/49
* Bump actions/checkout from 4 to 6 by @dependabot[bot] in https://github.com/TappNetwork/Filament-Form-Builder/pull/48
* Bump stefanzweifel/git-auto-commit-action from 5 to 7 by @dependabot[bot] in https://github.com/TappNetwork/Filament-Form-Builder/pull/51
* Bump dependabot/fetch-metadata from 2.4.0 to 2.5.0 by @dependabot[bot] in https://github.com/TappNetwork/Filament-Form-Builder/pull/50
* Add guest panel support for form pages by @scottgrayson in https://github.com/TappNetwork/Filament-Form-Builder/pull/53

**Full Changelog**: https://github.com/TappNetwork/Filament-Form-Builder/compare/v4.0.9...v4.1.0

## v4.0.9 - 2026-01-21

### What's Changed

* Add Filament 5 support by @andreia in https://github.com/TappNetwork/Filament-Form-Builder/pull/47

**Full Changelog**: https://github.com/TappNetwork/Filament-Form-Builder/compare/v4.0.8...v4.0.9

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
