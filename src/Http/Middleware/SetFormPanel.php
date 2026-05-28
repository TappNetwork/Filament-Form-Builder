<?php

declare(strict_types=1);

namespace Tapp\FilamentFormBuilder\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Filament\Http\Middleware\SetUpPanel;
use Filament\Models\Contracts\HasTenants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tapp\FilamentFormBuilder\Support\FormRoutePanelResolver;
use Tapp\FilamentFormBuilder\Support\FormRouteTenancy;

class SetFormPanel
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ?string $panel = null): Response
    {
        $panelId = FormRoutePanelResolver::resolve($panel);

        $setUpPanel = new SetUpPanel;

        return $setUpPanel->handle($request, function (Request $request) use ($next): Response {
            $this->bindTenantForFormRoute($request);

            return $next($request);
        }, $panelId);
    }

    protected function bindTenantForFormRoute(Request $request): void
    {
        $panel = Filament::getCurrentPanel();

        if (! $panel?->hasTenancy() || Filament::getTenant() instanceof Model) {
            return;
        }

        $tenant = FormRouteTenancy::resolveTenantFromRequest($request);

        if (! $tenant instanceof Model) {
            return;
        }

        $user = auth()->user();

        if ($user instanceof HasTenants && ! $user->canAccessTenant($tenant)) {
            abort(404);
        }

        Filament::setTenant($tenant, isQuiet: true);
    }
}
