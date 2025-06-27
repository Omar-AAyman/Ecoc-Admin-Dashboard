<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class RestrictToRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (!$user || $user->status !== 'active' || !$user->hasAnyRole($roles)) {
            throw new UnauthorizedHttpException('', 'Unauthorized access');
        }

        return $next($request);
    }
}
