<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\AppController;
use JWTAuth;
class ModuleAllowMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$modules)
    {
        $user = JWTAuth::user();
        if ($user->is_admin) return $next($request);
        $allowed_modules = AppController::get_allowed_modules();
        foreach($modules as $module)
        {
            if (in_array($module, $allowed_modules)) {
                return $next($request);
            }
        }
        return response('Низзя', 403);
    }
}
