<?php

declare(strict_types=1);

namespace Tapp\FilamentFormBuilder\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Panel;
use Tapp\FilamentFormBuilder\Models\FilamentForm;

class ShowForm extends Page
{
    public FilamentForm $form;

    protected static ?string $navigationLabel = null;

    protected static bool $shouldRegisterNavigation = false;

    public function getView(): string
    {
        // If authenticated and using app panel, use app panel view
        // Otherwise, use guest panel view
        if (auth()->check() && $this->getPanel()->getId() === config('filament-form-builder.app-panel-id', 'app')) {
            return 'filament-form-builder::pages.show-form-app';
        }

        return 'filament-form-builder::pages.show-form-guest';
    }

    public static function getRouteName(?Panel $panel = null): string
    {
        return 'filament-form-builder.show';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function getPanel(): Panel
    {
        // Use the current panel set by middleware (app for authenticated, guest for unauthenticated)
        $guestPanelId = config('filament-form-builder.guest-panel-id', 'guest');
        $appPanelId = config('filament-form-builder.app-panel-id', 'app');

        $currentPanel = Filament::getCurrentPanel();

        if ($currentPanel) {
            return $currentPanel;
        }

        // Fallback to guest panel if no current panel
        return Filament::getPanel($guestPanelId);
    }

    public function mount(FilamentForm $form): void
    {
        // If form doesn't permit guest entries and user is not authenticated, redirect to login
        if (! auth()->check() && ! $form->permit_guest_entries) {
            $loginRoute = config('filament-form-builder.login-route', 'filament.app.auth.login');

            $this->redirect(route($loginRoute, [
                'redirect' => request()->fullUrl(),
            ]), navigate: false);

            return;
        }

        // Show form (middleware sets the appropriate panel based on authentication)
        $this->form = $form->load('filamentFormFields');
    }

    public function getTitle(): string
    {
        return $this->form->name ?? 'Form';
    }
}
