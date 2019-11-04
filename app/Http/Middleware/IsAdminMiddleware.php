<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
class IsAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = JWTAuth::user();
        if ($user && $user->is_admin)
        {
            return $next($request);
        } else {
            return response('Низзя', 403);
        }
    }
}
