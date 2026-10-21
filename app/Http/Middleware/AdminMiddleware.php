<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        abort_unless($user, 401);
        abort_unless(isset($user->role) && $user->role === 'admin', 403, 'Administrator access is required.');

        return $next($request);
    }
}
