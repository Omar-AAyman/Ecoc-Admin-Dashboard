<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->status === 'inactive') {
                Auth::logout();
                return redirect()->route('login')->withErrors(['error' => 'Your account is inactive. Please contact the administrator.']);
            }
        }

        return $next($request);
    }
}