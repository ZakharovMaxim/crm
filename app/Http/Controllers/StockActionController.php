<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StockAction;
use App\Product;
use App\ProductStats;
use App\Http\Requests\StoreStockActionRequest;
use App\Http\Controllers\AppController;
class StockActionController extends Controller
{
    static private $module_id = 4;

    public function index(Request $request)
    {
        $type = $request->query('type');
        $is_submited = $request->query('is_submited');
        $stocks = $request->query('stocks');
        $per_page = $request->query('per_page') ? $request->query('per_page') : 20;
        $page = $request->query('page') ? $request->query('page') : 1;
        $stocks_ids = AppController::get_stocks_by_module(self::$module_id);
        $query = StockAction::where(['is_deleted' => 0])->orderBy('created_at','desc');
        if ($stocks_ids) {
            $query->where(function($query) use($stocks_ids) {
                $query->whereIn('to_stock', $stocks_ids);
                $query->orWhereIn('from_stock', $stocks_ids);
            });
        }
        if ($type == 1) {
            $query->where('type', 1);
        } else if ($type == 2) {
            $query->where('type', 2);
        } else if ($type == 3) {
            $query->where('type', 3);
        }
        if (isset($is_submited)) {
            if ($is_submited == 1) {
                $query->where('is_submited', 1);
            } else if ($is_submited == 0) {
                $query->where('is_submited', 0);
            }
        }
        if (isset($stocks)) {
            try {
                $query->where(function ($query) use ($stocks) {
                    $query->whereIn('to_stock', explode(',', $stocks));
                    $query->orWhereIn('from_stock', explode(',', $stocks));
                });
            } catch(Exception $e) {
            }
        }
        $total = $query->count();
        return ['data' => $query->take($per_page)->skip(($page - 1) * $per_page)->with('to_stock')->with('from_stock')->with('products')->with('products.images')->get(),
                'isOver' => $total <= $page * $per_page];
    }
    public function store(StoreStockActionRequest $request)
    {
        $action = StockAction::create($request->all());
        $action['products'] = [];
        $action['to_stock'] = $action->to_stock()->get()->first();
        if ($request->input('type') == 3) $action['from_stock'] = $action->from_stock()->get()->first();
        return $action;
    }
    public function add_products(StockAction $action, Request $request)
    {
        $result = [];
        foreach($request->input('rows') as $row)
        {
            $result[$row['id']] = [
                'count' => $row['count'],
                'purchase_price' => $row['purchase_price']
            ];
            if ($action->type == 1) {
                $to_stats = ProductStats::where(['is_deleted' => 0, 'stock_id' => $action->to_stock, 'product_id' => $row['id']])->get()->first();
                if (!$to_stats) {
                    $to_stats = ProductStats::create(['stock_id' => $action->to_stock, 'product_id' => $row['id'], 'in_prepare' => $row['count']]);
                } else {
                    $action->products;
                    $in_prepare = 0;
                    foreach($action['products'] as $product) {
    
                        $find = $product->id == $row['id'];
                        if ($find) {
                            $in_prepare = $product->pivot->count;
                        }
                    }
                    $to_stats->in_prepare = $to_stats->in_prepare - $in_prepare + $row['count'] < 0 ? 0 : $to_stats->in_prepare - $in_prepare + $row['count'];
                    $to_stats->save();
                }
            }
        }
        $action->products()->sync($result);
        return $action->products()->with(['product_stats' => function ($query) use($action){
            $query->where(['stock_id' => $action->to_stock]);
        }])->with('images')->get();
    }
    public function submit(StockAction $action, Request $request)
    {
        $result = [];
        foreach($request->input('rows') as $row)
        {
            $result[$row['id']] = [
                'count' => $row['count'],
                'purchase_price' => $row['purchase_price']
            ];
            $to_stats = ProductStats::where(['is_deleted' => 0, 'stock_id' => $action->to_stock, 'product_id' => $row['id']])->get()->first();
            if ($action->type == 1) {
                if (!$to_stats) {
                    $to_stats = ProductStats::create(['stock_id' => $action->to_stock, 'product_id' => $row['id'], 'in_stock' => $row['count']]);
                } else {
                    $to_stats->in_stock = $to_stats->in_stock + $row['count'];
                    $action->products;
                    $in_prepare = 0;
                    foreach($action['products'] as $product) {
    
                        $find = $product->id == $row['id'];
                        if ($find) {
                            $in_prepare = $product->pivot->count;
                        }
                    }
                    $to_stats->in_prepare = $to_stats->in_prepare - $in_prepare < 0 ? 0 : $to_stats->in_prepare - $in_prepare;
                    $to_stats->save();
                }
                // set product purchase price
                $product = Product::find($row['id']);
                $initial_price = $product->purchase_price ? $product->purchase_price : 0;
                $initial_in_stock = $to_stats['in_stock'] - $row['count'];
                $inital_sum = $initial_price * $initial_in_stock;
                $current_sum = $row['purchase_price'] * $row['count'];
                // return ['left' => ($inital_sum + $current_sum), 'righ' => ($initial_in_stock + $row['count']),'res' => ($inital_sum + $current_sum) / ($initial_in_stock + $row['count']), 'initial_price' => $initial_price, 'initial_in_stock' => $initial_in_stock, 'inital_sum' => $inital_sum, 'current_sum' => $current_sum, '$row[count]' => $row['count']];
                $product->purchase_price = ($inital_sum + $current_sum) / ($initial_in_stock + $row['count']);
                $product->save();
            } else if ($action->type == 2) {
                if (!$to_stats) abort(404);
                $to_stats->in_stock = $to_stats->in_stock - $row['count'];
                $to_stats->save();
            } else if ($action->type == 3) {
                $from_stats = ProductStats::where(['is_deleted' => 0, 'stock_id' => $action->from_stock, 'product_id' => $row['id']])->get()->first();
                if (!$from_stats) abort(404);
                $write_off_count = $row['count'] < $from_stats->in_stock ? $row['count'] : $from_stats->in_stock;
                $from_stats->in_stock = $from_stats->in_stock - $write_off_count;
                $from_stats->save();
                if (!$to_stats) {
                    $to_stats = ProductStats::create(['stock_id' => $action->to_stock, 'product_id' => $row['id'], 'in_stock' => $row['count']]);
                } else {
                    $to_stats->in_stock = $to_stats->in_stock + $write_off_count;
                    $to_stats->save();
                }
            }
        }
        $action->products()->sync($result);
        $action->is_submited = 1;
        $action->save();
        return $action->products()->with(['product_stats' => function ($query) use($action){
            $query->where(['stock_id' => $action->to_stock]);
        }])->with('images')->get();
    }
    public function delete_product(StockAction $action, $product, Request $request)
    {
        $action->products()->detach($product);
        if ($action->type != '1') return 'ok';
        $to_stats = ProductStats::where(['is_deleted' => 0, 'stock_id' => $action->to_stock, 'product_id' => $product])->get()->first();
        if (!isset($to_stats)) {
            abort(404);
        }
        $diff = $to_stats->in_prepare - $request->query('count') < 0 ? 0 : $to_stats->in_prepare - $request->query('count'); 
        $to_stats->in_prepare = $diff;
        $to_stats->save();
        
        return 'ok';
    }
    public function find(StockAction $action)
    {
        $action['products'] = $action->products()->with('images')->with(['product_stats' => function ($query) use($action){
            $query->where(['is_deleted' => 0, 'stock_id' => $action->to_stock]);
        }])->get();
        return $action;
    }
    public function update(StockAction $action, StoreStockActionRequest $request)
    {
        $action->update($request->all());
        return 'ok';
    }
    public function destroy(StockAction $action)
    {
        if ($action->is_submited) return 'nooooo ;)';
        if ($action->type == 1) {
            $action->products;
            foreach($action->products as $product) {
                $to_stats = ProductStats::where(['is_deleted' => 0, 'stock_id' => $action->to_stock, 'product_id' => $product->id])->get()->first();
                $to_stats->in_prepare = $to_stats->in_prepare - $product->pivot->count < 0 ? 0 : $to_stats->in_prepare - $product->pivot->count;
                $to_stats->save();
            }
        }
        $action->delete();
        return 'ok';
    }
}
