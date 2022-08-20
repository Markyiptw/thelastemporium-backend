<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;

class VerifyUser
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!(auth()->user() instanceof User)) {
            abort(403);
        }

        return $next($request);
    }
}
