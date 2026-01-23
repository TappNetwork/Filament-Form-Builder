<?php

declare(strict_types=1);

namespace Tapp\FilamentFormBuilder\Http\Middleware;

use Closure;
use Filament\Http\Middleware\SetUpPanel;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetFormPanel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ?string $panel = null): Response
    {
        // If panel is not provided, determine based on authentication
        if ($panel === null) {
            $appPanelId = config('filament-form-builder.app-panel-id', 'app');
            $guestPanelId = config('filament-form-builder.guest-panel-id', 'guest');

            $panel = auth()->check() ? $appPanelId : $guestPanelId;
        }

        // Use SetUpPanel middleware to properly initialize the panel
        // This ensures the panel's layout and middleware are applied correctly
        $setUpPanel = new SetUpPanel;

        return $setUpPanel->handle($request, function ($request) use ($next) {
            return $next($request);
        }, $panel);
    }
}
