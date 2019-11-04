<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
class YourselvesAllowMiddleware
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
        $requested_user = $request->route('user');
        $user = JWTAuth::user();
        if ($user->is_admin || $user->id == $requested_user->id) {
            return $next($request);
        } else {
            return response('Низзя', 403);
        }
    }
}
