<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\Session;

class AuthenticateSession
{
    public function handle($request, Closure $next)
    {
        $sessionId = $request->cookie('session_token');

        if (!$sessionId) {
            return redirect()->route('login');
        }

        $session = Session::where('sessionId', $sessionId)
            ->where('expiresAt', '>', now())
            ->first();

        if (!$session) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        // Update last used time (optional)
        $session->touch();

        // Bind user to request
        Auth::login($session->user);

        return $next($request);
    }
}
