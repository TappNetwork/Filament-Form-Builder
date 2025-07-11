<?php

namespace Tapp\FilamentFormBuilder\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tapp\FilamentFormBuilder\Models\FilamentForm;

class CheckFormGuestAccess
{
    public function handle(Request $request, Closure $next)
    {
        // If user is authenticated, allow access
        if (Auth::check()) {
            return $next($request);
        }

        $form = $request->route('form');

        // If form allows guest entries, allow access
        if ($form instanceof FilamentForm && $form->permit_guest_entries) {
            return $next($request);
        }

        // Otherwise, redirect to login
        return redirect()->guest(route('login'));
    }
}
