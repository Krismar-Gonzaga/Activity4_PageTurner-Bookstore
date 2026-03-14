<?php
// app/Http/Middleware/TwoFactorPartial.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TwoFactorPartial
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user has partially authenticated (has user ID in session but not fully logged in)
        if (!Session::has('two_factor:user:id')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}