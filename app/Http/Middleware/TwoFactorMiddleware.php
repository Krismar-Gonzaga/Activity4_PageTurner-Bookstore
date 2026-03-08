<?php
// app/Http/Middleware/TwoFactorMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->two_factor_enabled && !session('two_factor_authenticated')) {
            return redirect()->route('two-factor.verify');
        }

        return $next($request);
    }
}