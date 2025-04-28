<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerPolicies();

        Auth::viaRequest('session', function ($request) {
            $sessionId = $request->cookie('session_token');

            if (!$sessionId) {
                return null;
            }

            $session = \App\Models\Session::where('sessionId', $sessionId)
                ->where('expiresAt', '>', now())
                ->first();

            return $session ? $session->user : null;
        });
    }
}
