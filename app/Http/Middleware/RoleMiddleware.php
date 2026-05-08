<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        // No autenticado
        if (!$user) {
            abort(403, 'No autorizado');
        }

        // ADMIN puede entrar a todo
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Validar rol específico
        if ($user->role !== $role) {
            abort(403, 'No autorizado');
        }

        return $next($request);
    }
}