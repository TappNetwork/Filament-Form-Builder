<?php

declare(strict_types=1);

namespace Tapp\FilamentFormBuilder\Support;

use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Http\Request;

final class FormRoutePanelResolver
{
    public static function resolve(?string $forcedPanelId = null): string
    {
        if (is_string($forcedPanelId) && $forcedPanelId !== '') {
            return $forcedPanelId;
        }

        $guestPanelId = config('filament-form-builder.guest-panel-id', 'guest');
        $appPanelId = config('filament-form-builder.app-panel-id', 'app');

        if (! auth()->check()) {
            return $guestPanelId;
        }

        $adminPanelId = config('filament-form-builder.admin-panel-id');

        if (is_string($adminPanelId) && $adminPanelId !== '' && self::shouldUseAdminPanel(request(), $adminPanelId)) {
            return $adminPanelId;
        }

        return $appPanelId;
    }

    public static function usesAuthenticatedFormLayout(?Panel $panel = null): bool
    {
        if (! auth()->check()) {
            return false;
        }

        $panel ??= Filament::getCurrentPanel();

        if (! $panel instanceof Panel) {
            return false;
        }

        $authenticatedPanelIds = array_values(array_filter([
            config('filament-form-builder.app-panel-id', 'app'),
            config('filament-form-builder.admin-panel-id'),
        ], fn (mixed $panelId): bool => is_string($panelId) && $panelId !== ''));

        return in_array($panel->getId(), $authenticatedPanelIds, true);
    }

    protected static function shouldUseAdminPanel(Request $request, string $adminPanelId): bool
    {
        try {
            $adminPanel = Filament::getPanel($adminPanelId);
        } catch (\Throwable) {
            return false;
        }

        $user = auth()->user();

        if (! $user instanceof FilamentUser || ! $user->canAccessPanel($adminPanel)) {
            return false;
        }

        $referer = (string) $request->headers->get('referer', '');
        $adminPath = '/'.trim((string) $adminPanel->getPath(), '/');

        if ($adminPath !== '/' && str_contains($referer, $adminPath)) {
            return true;
        }

        return (bool) config('filament-form-builder.prefer-admin-panel-for-authenticated-form-routes', false);
    }
}
