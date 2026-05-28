<?php

declare(strict_types=1);

namespace Tapp\FilamentFormBuilder\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Tapp\FilamentFormBuilder\Models\FilamentForm;
use Tapp\FilamentFormBuilder\Models\FilamentFormUser;
use Tapp\FilamentFormBuilder\Models\Traits\BelongsToTenant;

final class FormRouteTenancy
{
    public static function isEnabled(): bool
    {
        return (bool) config('filament-form-builder.tenancy.enabled', false);
    }

    /**
     * Resolve the tenant model for a public form or entry route.
     */
    public static function resolveTenantFromRequest(Request $request): ?Model
    {
        if (! self::isEnabled()) {
            return null;
        }

        $tenantModel = config('filament-form-builder.tenancy.model');

        if (! is_string($tenantModel) || $tenantModel === '') {
            return null;
        }

        $entry = $request->route('entry');

        if ($entry instanceof FilamentFormUser) {
            return self::resolveTenantForRecord($entry, $tenantModel);
        }

        $form = $request->route('form');

        if ($form instanceof FilamentForm) {
            return self::resolveTenantForRecord($form, $tenantModel);
        }

        return null;
    }

    /**
     * @param  class-string<Model>  $tenantModel
     */
    public static function resolveTenantForRecord(Model $record, ?string $tenantModel = null): ?Model
    {
        if (! self::isEnabled()) {
            return null;
        }

        $tenantModel ??= config('filament-form-builder.tenancy.model');

        if (! is_string($tenantModel) || $tenantModel === '') {
            return null;
        }

        $relationshipName = BelongsToTenant::getTenantRelationshipName();
        $columnName = BelongsToTenant::getTenantColumnName();

        if (method_exists($record, $relationshipName)) {
            /** @var Model|null $tenant */
            $tenant = $record->relationLoaded($relationshipName)
                ? $record->getRelation($relationshipName)
                : $record->{$relationshipName}()->first();

            if ($tenant instanceof Model) {
                return $tenant;
            }
        }

        $tenantId = $record->getAttribute($columnName);

        if ($tenantId === null) {
            if ($record instanceof FilamentFormUser) {
                $record->loadMissing('filamentForm');

                $parentForm = $record->filamentForm;

                if ($parentForm instanceof FilamentForm) {
                    return self::resolveTenantForRecord($parentForm, $tenantModel);
                }
            }

            return null;
        }

        return $tenantModel::query()->find($tenantId);
    }
}
