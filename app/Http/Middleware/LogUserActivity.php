<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;

use Closure;
use Illuminate\Http\Request;

class LogUserActivity
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {

            log_activity(
                'viewed',
                'Visited page',
                null,
                null,
                ['url' => $request->fullUrl()]
            );
        }

        return $next($request);
    }
}
