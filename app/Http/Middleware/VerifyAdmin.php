<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;

class VerifyAdmin
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
        if (!(auth()->user() instanceof Admin)) {
            abort(403);
        }

        return $next($request);
    }
}
