<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\AppController;
use App\Shop;
class StockShopAllowMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $module_id)
    {
        $allowed_ids = AppController::get_shops_by_module($module_id);
        if (!$allowed_ids) {
            return $next($request);
        }
        $stock_ids = [];
        if ($request->stock_id) {
            $stock_ids[] = $request->stock_id;
        }
        $action = $request->route('action');
        if ($action) {
            if ($action->from_stock) {
                $stock_ids[] = $action->from_stock;
            }
            $stock_ids[] = $action->to_stock;
        }
        $shops = Shop::where(['is_deleted' => 0])->whereIn('stock_id', $stock_ids)->get();
        $is_ok = true;

        foreach($shops as $shop)
        {
            if (!in_array($shop->id, $allowed_ids)) $is_ok = false;
        }

        if ($is_ok) {
            return $next($request);
        }
        return response('Ты кто?', 403);
    }
}
