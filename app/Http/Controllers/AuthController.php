<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Models\EmployeeLog;
use App\Models\SessionTrack;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Create employee log for login
            \App\Models\EmployeeLog::safeCreate([
                'user_id' => $user->id,
                'event' => 'login',
                'logged_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => $request->session()->getId(),
                'context' => []
            ]);

            // Create or update session track (only if table exists)
            if (Schema::hasTable('session_tracks')) {
                SessionTrack::create([
                    'user_id' => $user->id,
                    'session_id' => $request->session()->getId(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'started_at' => now(),
                    'last_activity_at' => now(),
                ]);
            }

            // Redirect based on role
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->isInventory()) {
                return redirect()->route('inventory.dashboard');
            } elseif ($user->isCashier()) {
                return redirect()->route('cashier.dashboard');
            } elseif ($user->isHelper()) {
                return redirect()->route('helper.dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();

            $sessionId = $request->session() ? $request->session()->getId() : null;

            // Mark session ended (only if table exists)
            if (Schema::hasTable('session_tracks') && $sessionId) {
                \App\Models\SessionTrack::where('session_id', $sessionId)
                    ->where('user_id', $user->id)
                    ->update(['ended_at' => now(), 'last_activity_at' => now()]);
            }

            // Create employee log for logout
            \App\Models\EmployeeLog::safeCreate([
                'user_id' => $user->id,
                'event' => 'logout',
                'logged_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => $sessionId,
                'context' => []
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
