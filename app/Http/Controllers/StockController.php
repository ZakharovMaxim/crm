<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreStockRequest;
use App\Stock;
use App\StockAction;
use App\Folder;
use App\Product;
use App\Http\Controllers\AppController;
class StockController extends Controller
{
    static private $module_id = 4;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stocks = AppController::get_stocks_by_module(self::$module_id);
        $query = StockAction::where(['is_deleted' => 0]);
        if ($stocks) {
            $query->where(function ($query) use($stocks) {
                $query->whereIn('to_stock', $stocks);
                $query->orWhereIn('from_stock', $stocks);
            });
        }
        $stats = $query->get()->groupBy('type');
        $statsResult = [
            'total_enrollment' => 0,
            'submited_enrollment' => 0,
            'created_enrollment' => 0,
            'total_writeoff' => 0,
            'submited_writeoff' => 0,
            'created_writeoff' => 0,
            'total_movement' => 0,
            'submited_movement' => 0,
            'created_movement' => 0,
        ];
        foreach($stats as $group) {
            foreach($group as $stat) {
                if ($stat->type == 1) {
                    $statsResult['total_enrollment']++;
                    if ($stat->is_submited) {
                        $statsResult['submited_enrollment']++;
                    } else {
                        $statsResult['created_enrollment']++;
                    }
                } else if ($stat->type == 2) {
                    $statsResult['total_writeoff']++;
                    if ($stat->is_submited) {
                        $statsResult['submited_writeoff']++;
                    } else {
                        $statsResult['created_writeoff']++;
                    }
                } else if ($stat->type == 3) {
                    $statsResult['total_movement']++;
                    if ($stat->is_submited) {
                        $statsResult['submited_movement']++;
                    } else {
                        $statsResult['created_movement']++;
                    }
                }
            }
            
        }
        $stock_query =  Stock::where(['is_deleted' => 0]);
        if ($stocks) {
            $stock_query->whereIn('id', $stocks);
        }
        return ['stocks' => $stock_query->get(), 'stats' => $statsResult];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return ['folders' => Folder::query()->where(['parent_id' => null])->get()];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreStockRequest $request)
    {
        return Stock::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Stock $stock)
    {
        return $stock;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Stock $stock)
    {
        $stock->folders;
        // return AppController::get_allowed_catalogs();
        return ['folders' => Folder::query()->where(['parent_id' => null])->get(), 'stock' => $stock];
    }
    public function update_folders(Stock $stock, Request $request)
    {
        $stock->folders()->sync($request->input('ids'));
        return 'ok';
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreStockRequest $request, Stock $stock)
    {
        $stock->update($request->all());
        return 'ok';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stock $stock)
    {
        $stock->is_deleted = true;
        $stock->save();
        return 'ok';
    }

    public function get_products(Stock $stock, Request $request)
    {
        $catalogs = $stock->folders()->get();
        $page = $request->query('page') ? $request->query('page') : 1;
        $per_page = $request->query('per_page') ? $request->query('per_page') : 20;
        $queryString = $request->query('query');
        $name = $request->query('name') ? $request->query('name') : 'name';
        $orderBy = $request->query('order_by') ? $request->query('order_by') : 'id';
        $direction = $request->query('order_direction') ? $request->query('order_direction') : 'desc';
        $exclude_ids = $request->query('exclude_ids');
        $query = Product::where(['is_deleted' => 0, 'is_variation' => 0])
                        ->orderBy($orderBy, $direction);
        if (isset($exclude_ids) && $exclude_ids) {
            try {
                $exclude_ids = explode(',', $exclude_ids);
                $query->whereNotIn('id', $exclude_ids);
            } catch(Exception $e) {
            }
        }
        if ($name && $queryString) {
            $query->where($name, 'like', '%'.$queryString.'%');
        }
        $ids = [];
        if (count($catalogs)) {
            foreach($catalogs as $catalog)
            {
                $ids[] = $catalog->id;
            }
        } else {
            $ids = AppController::get_allowed_catalogs();
        }
        if (is_array($ids)) $query->whereIn('root_id', $ids);
        $query->with('images')->with(['product_stats' => function ($query) use($stock){
            $query->where(['stock_id' => $stock->id]);
        }]);
        $total = $query->count();
        $items = $query->skip(($page - 1) * $per_page)
                        ->take($per_page)->get();
        return ['items' => $items, 'isOver' => $total <= $page * $per_page, 'total' => $total, 'page' => $page, 'per' => $per_page, 'mul' => $page * $per_page, 'exp' => $total >= $page * $per_page];
    }
}
