<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\AppController;

class CatalogAllowMiddleware
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
        $allowed_catalogs = AppController::get_allowed_catalogs();
        if (!$allowed_catalogs) return $next($request);

        $requested_id = $request->query('parent_id');
        $instance = $request->route('product');
        if ($instance && $instance->root_id) {
            $requested_id = $instance->root_id;
        }
        if ($request->root_id) $requested_id = $request->root_id;
        if (!$requested_id || in_array($requested_id, $allowed_catalogs)) {
            return $next($request);
        }
        return response("Неа", 403);
    }
}
