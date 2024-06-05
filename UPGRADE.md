# Upgrade Guide
## Upgrading to to 1.1 from 1.0
### Allow Guest Entries by Making User Nullable on FilamentFormUser
1.1 supports nullable user ids so that guest data can be collected by forms. If you are upgrading from 1.0 to 1.1, create a migration with the following methods to reflect this change.
```
    Schema::table('filament_form_user', function (Blueprint $table) {
        $table->foreignId('user_id')->nullable()->change();
    });

    Schema::table('filament_forms', function (Blueprint $table) {
        $table->boolean('permit_guest_entries')->default(false);
    });
```
### Support user configurable redirect URL
1.1 supports user configurable redirect URLs. When a redirect URL is present on the form model, the user will be redirected there instead of the redirect URL specified in the config. If you are upgrading from 1.0 to 1.1, create a migration with the following method to reflect this change.
```
    Schema::table('filament_forms', function (Blueprint $table) {
        $table->text('redirect_url')->nullable();
    });
```
