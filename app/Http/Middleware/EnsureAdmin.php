<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || ! in_array($user->role, ['superadmin', 'store_admin'], true) || $user->status !== 'active') {
            if ($user) {
                auth()->logout();
            }
            if ($request->expectsJson()) {
                abort(403);
            }

            return redirect()->route('home');
        }

        return $next($request);
    }
}
