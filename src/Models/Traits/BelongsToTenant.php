<?php

namespace Tapp\FilamentFormBuilder\Models\Traits;

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

        // Register the dynamic relationship
        static::resolveRelationUsing(
            static::getTenantRelationshipName(),
            function ($model) {
                return $model->belongsTo(config('filament-form-builder.tenancy.model'), static::getTenantColumnName());
            }
        );

        // Automatically set tenant_id when creating a new model
        static::creating(function ($model) {
            $tenantColumnName = static::getTenantColumnName();

            // Skip if tenant foreign key is already set (e.g., by Filament's observer)
            if (! empty($model->{$tenantColumnName})) {
                return;
            }

            $tenantRelationshipName = static::getTenantRelationshipName();

            // Try to get tenant from Filament context (Filament's standard method)
            // This handles top-level resources created outside Filament's Resource observers
            if (class_exists(\Filament\Facades\Filament::class)) {
                $tenant = \Filament\Facades\Filament::getTenant();
                if ($tenant) {
                    $model->{$tenantRelationshipName}()->associate($tenant);

                    return;
                }
            }

            if (method_exists($model, 'filamentForm') && isset($model->filament_form_id)) {
                $parentFormId = $model->filament_form_id;
                $parentFormClass = get_class($model->filamentForm()->getRelated());
                $parentForm = $parentFormClass::find($parentFormId);

                if ($parentForm) {
                    $parentTenant = $parentForm->{$tenantRelationshipName};
                    if ($parentTenant) {
                        $model->{$tenantRelationshipName}()->associate($parentTenant);
                    }
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
