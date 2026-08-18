<?php

namespace Tapp\FilamentFormBuilder\Models\Traits;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

trait BelongsToTenant
{
    /**
     * Boot the trait and register the dynamic tenant relationship.
     */
    public static function bootBelongsToTenant(): void
    {
        if (! config('filament-form-builder.tenancy.enabled')) {
            return;
        }

        // Register the dynamic relationship. The relation name must be passed explicitly:
        // Eloquent otherwise guesses it from the backtrace, which resolves to this closure
        // and breaks `associate()` and any later `refresh()` / `load()` on the model.
        static::resolveRelationUsing(
            static::getTenantRelationshipName(),
            function ($model) {
                return $model->belongsTo(
                    config('filament-form-builder.tenancy.model'),
                    static::getTenantColumnName(),
                    null,
                    static::getTenantRelationshipName(),
                );
            }
        );

        // Automatically set tenant_id when creating a new model
        static::creating(function ($model) {
            $tenantColumnName = static::getTenantColumnName();

            // Skip if tenant foreign key is already set (e.g., by Filament's observer)
            if (! empty($model->{$tenantColumnName})) {
                return;
            }

            // Try to get tenant from Filament context (Filament's standard method)
            // This handles top-level resources created outside Filament's Resource observers
            if (class_exists(Filament::class)) {
                $tenant = Filament::getTenant();
                if ($tenant) {
                    $model->{$tenantColumnName} = $tenant->getKey();

                    return;
                }
            }

            if (method_exists($model, 'filamentForm') && isset($model->filament_form_id)) {
                $parentFormId = $model->filament_form_id;
                $parentFormRelated = $model->filamentForm()->getRelated();

                $parentFormClass = $parentFormRelated::class;
                $parentForm = $parentFormClass::find($parentFormId);

                if ($parentForm) {
                    $model->{$tenantColumnName} = $parentForm->{$tenantColumnName};
                }
            }
        });
    }

    /**
     * Get the tenant relationship name.
     */
    public static function getTenantRelationshipName(): string
    {
        // Use configured relationship name if provided
        if ($relationshipName = config('filament-form-builder.tenancy.relationship_name')) {
            return $relationshipName;
        }

        // Auto-detect from tenant model class name
        $tenantModel = config('filament-form-builder.tenancy.model');

        if (! $tenantModel) {
            if (config('filament-form-builder.tenancy.enabled')) {
                throw new \Exception('Tenant model not configured in filament-form-builder.tenancy.model');
            }

            return 'tenant'; // Return a default value when tenancy is disabled
        }

        return Str::snake(class_basename($tenantModel));
    }

    /**
     * Get the tenant column name.
     */
    public static function getTenantColumnName(): string
    {
        // Use configured column name if provided
        if ($columnName = config('filament-form-builder.tenancy.column')) {
            return $columnName;
        }

        // Auto-detect from tenant model class name
        return static::getTenantRelationshipName().'_id';
    }

    /**
     * Get the tenant relationship instance.
     * This provides a typed method for IDEs and static analysis.
     */
    public function tenant(): ?BelongsTo
    {
        if (! config('filament-form-builder.tenancy.enabled')) {
            return null;
        }

        $tenantModel = config('filament-form-builder.tenancy.model');

        if (! $tenantModel) {
            throw new \Exception('Tenant model not configured in filament-form-builder.tenancy.model');
        }

        return $this->belongsTo($tenantModel, static::getTenantColumnName());
    }
}
