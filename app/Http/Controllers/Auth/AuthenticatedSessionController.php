<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        if ($user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['Administrador', 'Supervisor'])) {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }

        if ($user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['Finanzas'])) {
            return redirect()->intended(route('admin.finanzas.index', absolute: false));
        }

        if ($user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['Encargado', 'Líder'])) {
            return redirect()->intended(route('admin.actividades.mis', absolute: false));
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
