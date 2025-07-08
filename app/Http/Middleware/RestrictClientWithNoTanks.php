<?php

namespace App\Http\Middleware;

use App\Models\Tank;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestrictClientWithNoTanks
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isClient() && (!$user->company_id || !Tank::where('company_id', $user->company_id)->exists())) {
                Auth::logout();
                return redirect()->route('login')->withErrors(['error' => 'No tanks assigned to your company. Please contact the administrator.']);
            }
        }

        return $next($request);
    }
}
