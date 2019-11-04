<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\AppController;

class ShopAllowMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $instance, $module_id)
    {
        $allowed_ids = AppController::get_shops_by_module($module_id);
        if (!$allowed_ids) {
            return $next($request);
        }
        $requested_ids = [];
        if ($request->shop_id) {
            $requested_ids[] = $request->shop_id;
        }
        if ($request->field && $request->field === 'shop_id' && $request->value) {
            $requested_ids[] = $request->value;
        }
        $instanceObj = $request->route($instance);
        if ($instanceObj && is_object($instanceObj) && $instanceObj->shop_id) {
            $requested_ids[] = $instanceObj->shop_id;
        }
        $is_ok = true;
        foreach($requested_ids as $id)
        {
            if (!in_array($id, $allowed_ids)) {
                $is_ok = false;
            }
        }
        if ($is_ok) {
            return $next($request);
        } else {
            return response([
                '$requested_ids' => $requested_ids,
                'is_ok' => $is_ok
            ], 403);
        }
    }
}
