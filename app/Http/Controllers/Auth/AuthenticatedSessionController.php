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
     * Mostrar login
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Procesar login
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // ADMIN
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // RECEPCIONISTA
        if ($user->role === 'recepcionista') {
            return redirect()->route('recepcionista.dashboard');
        }

        // Si no tiene rol válido
        Auth::logout();

        return redirect('/')
            ->withErrors([
                'email' => 'El usuario no tiene un rol válido.'
            ]);
    }

    /**
     * Cerrar sesión
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}