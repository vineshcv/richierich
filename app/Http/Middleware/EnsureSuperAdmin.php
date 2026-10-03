<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role !== 'superadmin') {
            if ($request->is('api/*') || $request->expectsJson()) {
                abort(403);
            }

            return redirect()->route('admin.dashboard')->withErrors([
                'user' => 'Only a superadmin can open that page.',
            ]);
        }

        return $next($request);
    }
}
