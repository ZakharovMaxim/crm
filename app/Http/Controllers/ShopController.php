<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AppController;
use Illuminate\Http\Request;
use App\Http\Requests\ShopCreateRequest;
use App\Shop;
use App\Order;
use App\Payment;
use App\Plugin;
use App\ProductStats;
use App\Stock;
use App\StockAction;

class ShopController extends Controller
{
    static private $module_id = 1;

    private function check($shop_id = 0)
    {
        $shops_ids = AppController::get_shops_by_module(self::$module_id);
        if ($shop_id && $shops_ids) {
            return in_array($shop_id, $shops_ids);
        }
        return 1;
    }
    private function restricted()
    {
        return response('No', 403);
    }

    public function index() {
        $response['shops'] = Shop::query(self::$module_id)->get();
        $response['plugins'] = Plugin::where(['is_deleted' => 0])->get();
        return $response;
    }
    public function store(ShopCreateRequest $request) {
        $stock = Stock::create(['name' => $request->input('name')]);
        $shop = Shop::create(array_merge($request->all(), ['stock_id' => $stock->id]));
        // $shop['stock'] = $stock;
        return $shop;
    }
    public function show(Shop $shop) {
        if ($this->check($shop->id)) {
            return $shop;
        }
        return $this->restricted();
    }
    public function destroy(Shop $shop) {
        if (!$this->check($shop->id)) {
            return $this->restricted();
        }
        Stock::where(['id' => $shop->stock_id])->update(['is_deleted' => 1]);
        StockAction::where(['to_stock' => $shop->stock_id])->orWhere(['from_stock' => $shop->stock_id])->update(['is_deleted' => 1]);
        ProductStats::where(['stock_id' => $shop->stock_id])->update(['is_deleted' => 1]);
        Order::where(['shop_id' => $shop->id])->update(['status' => 15]);
        Payment::where(['shop_id' => $shop->id])->update(['is_deleted' => 1]);
        $shop->is_deleted = true;
        $shop->name = $shop->name.'[DELETED]';
        $shop->save();
        return 'ok';
    }
    public function update(ShopCreateRequest $request, Shop $shop) {
        if ($request->input('name') != $shop->name) {
            Stock::where(['id' => $shop->stock_id])->update(['name' => $request->input('name')]);
        }
        $shop->update($request->all());
        return 'success';
    }
}
