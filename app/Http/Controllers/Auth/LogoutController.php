<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        $user = Auth::user();
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'user.logged_out',
            'description' => 'User ' . $user->email . ' logged out',
            'model_type' => \App\Models\User::class,
            'model_id' => $user->id,
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
