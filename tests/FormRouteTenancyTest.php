<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Tapp\FilamentFormBuilder\Models\FilamentForm;
use Tapp\FilamentFormBuilder\Models\FilamentFormUser;
use Tapp\FilamentFormBuilder\Support\FormRouteTenancy;

beforeEach(function (): void {
    FormRouteTenancyTestTenant::$findResult = null;
});

it('resolves tenant from entry company id column', function (): void {
    config()->set('filament-form-builder.tenancy.enabled', true);
    config()->set('filament-form-builder.tenancy.model', FormRouteTenancyTestTenant::class);
    config()->set('filament-form-builder.tenancy.relationship_name', 'company');
    config()->set('filament-form-builder.tenancy.column', 'company_id');

    $tenant = new FormRouteTenancyTestTenant;
    $tenant->forceFill(['id' => 42, 'name' => 'Acme']);

    FormRouteTenancyTestTenant::$findResult = $tenant;

    $entry = new FilamentFormUser;
    $entry->forceFill(['company_id' => 42]);

    $request = mockFormRouteRequest('entry', $entry);

    expect(FormRouteTenancy::resolveTenantFromRequest($request))
        ->toBe($tenant);
});

it('returns null when tenancy is disabled', function (): void {
    config()->set('filament-form-builder.tenancy.enabled', false);

    $entry = new FilamentFormUser;
    $entry->forceFill(['company_id' => 42]);

    $request = mockFormRouteRequest('entry', $entry);

    expect(FormRouteTenancy::resolveTenantFromRequest($request))->toBeNull();
});

it('resolves tenant from form record', function (): void {
    config()->set('filament-form-builder.tenancy.enabled', true);
    config()->set('filament-form-builder.tenancy.model', FormRouteTenancyTestTenant::class);
    config()->set('filament-form-builder.tenancy.relationship_name', 'company');
    config()->set('filament-form-builder.tenancy.column', 'company_id');

    $tenant = new FormRouteTenancyTestTenant;
    $tenant->forceFill(['id' => 7, 'name' => 'Beta']);

    FormRouteTenancyTestTenant::$findResult = $tenant;

    $form = new FilamentForm;
    $form->forceFill(['company_id' => 7]);

    $request = mockFormRouteRequest('form', $form);

    expect(FormRouteTenancy::resolveTenantFromRequest($request))
        ->toBe($tenant);
});

/**
 * @param  'entry'|'form'  $parameter
 */
function mockFormRouteRequest(string $parameter, FilamentForm|FilamentFormUser $model): Request
{
    $request = Request::create('/test/1');

    $route = new Route('GET', '/test/{'.$parameter.'}', fn (): string => '');
    $route->bind($request);
    $route->setParameter($parameter, $model);

    $request->setRouteResolver(fn (): Route => $route);

    return $request;
}

final class FormRouteTenancyTestTenant extends Model
{
    public static ?self $findResult = null;

    protected $table = 'companies';

    public $timestamps = false;

    public static function query(): FormRouteTenancyTestTenantQuery
    {
        return new FormRouteTenancyTestTenantQuery;
    }
}

final class FormRouteTenancyTestTenantQuery
{
    public function find(mixed $id): ?FormRouteTenancyTestTenant
    {
        return FormRouteTenancyTestTenant::$findResult;
    }
}
