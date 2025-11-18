<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isAdmin()) {
            throw new AuthenticationException('Administrateur requis.');
        }

        $token = $user->currentAccessToken();

        if ($token && ! $token->can('admin')) {
            abort(403, 'Ce jeton ne possède pas les permissions nécessaires.');
        }

        return $next($request);
    }
}
