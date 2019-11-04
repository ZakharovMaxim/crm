<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Product;
use App\Image;
use App\Folder;
use App\Shop;
use App\Stock;
use App\PriceGroup;
use App\Trademark;
use App\Attribute;
use App\Http\Requests\StoreProductRequest;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AppController;

class ProductController extends Controller
{
    static private $module_id = 2;
    static private $module_report_stock_id = 8;
    static private $module_report_order_id = 9;
    public function import(Request $request) {
        $allowed_catalogs = AppController::get_allowed_catalogs();
        $i = 0;
        $columns = [];
        $test = [];
        if (($handle = fopen($request->file('file'), "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $length = count($data);
            $row = [];
            for ($c=0; $c < $length; $c++) {
                if ($i === 0) {
                    if ($data[$c] === 'Код товара') {
                        $columns[$c] = 'id';
                    }
                    if ($data[$c] === 'Название товара') {
                        $columns[$c] = 'name';
                    }
                    if ($data[$c] === 'Артикул товара') {
                        $columns[$c] = 'sku';
                    }
                    if ($data[$c] === 'Дополнительная информация') {
                        $columns[$c] = 'additional_info';
                    }
                    if ($data[$c] === 'Описание товара') {
                        $columns[$c] = 'description';
                    }
                    if ($data[$c] === 'Цена продажи') {
                        $columns[$c] = 'selling_price';
                    }
                    if ($data[$c] === 'Цена закупки') {
                        $columns[$c] = 'purchase_price';
                    }
                    if ($data[$c] === 'Минимальное количество') {
                        $columns[$c] = 'min_count';
                    }
                    if ($data[$c] === 'Корневой каталог') {
                        $columns[$c] = 'root_id';
                    }
                } else {
                    if (isset($columns[$c])) {
                        $is_num = $columns[$c] === 'purchase_price' || $columns[$c] === 'selling_price' || $columns[$c] === 'min_count';
                        $default_value = $is_num ? 0 : '';
                        $row[$columns[$c]] = $data[$c] ? $data[$c] : $default_value;
                    }
                }
            }
            // if you try to crete new products you must set root_id in csv file
            if ($i !== 0 && isset($row['root_id']) && $row['root_id']) {
                // return $row;
                if (!$allowed_catalogs || in_array($row['root_id'], $allowed_catalogs)) {
                    Product::updateOrInsert(['id' => isset($row['id']) ? $row['id'] : null], $row);
                }
            }
            $i++;
        }
        // return $test;
        fclose($handle);
        }
    }
    public function export(Request $request) {
        $allowed_catalogs = AppController::get_allowed_catalogs();
        $fields = $request->query('fields') ? $request->query('fields') : '';
        $catalog_id = strpos($fields, 'catalog_id') !== false;
        $catalog_name = strpos($fields, 'catalog_name') !== false;
        $product_name = strpos($fields, 'product_name') !== false;
        $product_sku = strpos($fields, 'product_sku') !== false;
        $product_additional = strpos($fields, 'product_additional') !== false;
        $product_description = strpos($fields, 'product_description') !== false;
        $trademark_id = strpos($fields, 'trademark_id') !== false;
        $trademark_name = strpos($fields, 'trademark_name') !== false;
        $selling_price = strpos($fields, 'selling_price') !== false;
        $purchase_price = strpos($fields, 'purchase_price') !== false;
        $min_count = strpos($fields, 'min_count') !== false;
        $query = Product::with('trademark')->with('catalog');
        if (is_array($allowed_catalogs)) {
            $query->whereIn('root_id', $allowed_catalogs);
        }
        $products = $query->get();
        $columns = ['Код товара'];
        if ($product_name) {
            $columns[] = 'Название товара';
        }
        if ($product_sku) {
            $columns[] = 'Артикул товара';
        }
        if ($catalog_id) {
            $columns[] = 'Код каталога';
        }
        if ($catalog_name) {
            $columns[] = 'Название каталога';
        }
        if ($product_additional) {
            $columns[] = 'Дополнительная информация';
        }
        if ($product_description) {
            $columns[] = 'Описание товара';
        }
        if ($trademark_id) {
            $columns[] = 'Код торговой марки';
        }
        if ($trademark_name) {
            $columns[] = 'Название торговой марки';
        }
        if ($selling_price) {
            $columns[] = 'Цена продажи';
        }
        if ($purchase_price) {
            $columns[] = 'Цена закупки';
        }
        if ($min_count) {
            $columns[] = 'Минимальное количество';
        }
        $columns[] = 'Корневой каталог';
        $date = date_create();
        $filename = date_timestamp_get($date) . '_catalog_export.csv';
        $file = fopen($filename, 'w');
        fputcsv($file, $columns);
        
        foreach($products as $product) {
            $row = [$product->id];
            if ($product_name) {
                $row[] = $product->name;
            }
            if ($product_sku) {
                $row[] = $product->sku;
            }
            if ($catalog_id) {
                $row[] = $product->catalog_id;
            }
            if ($catalog_name) {
                $row[] = $product->catalog->name;
            }
            if ($product_additional) {
                $row[] = $product->additional_info;
            }
            if ($product_description) {
                $row[] = $product->description;
            }
            if ($trademark_id) {
                $row[] = $product->trademark_id;
            }
            if ($trademark_name) {
                $row[] = $product->trademark ? $product->trademark->name : '';
            }
            if ($selling_price) {
                $row[] = $product->selling_price;
            }
            if ($purchase_price) {
                $row[] = $product->purchase_price;
            }
            if ($min_count) {
                $row[] = $product->min_count;
            }
            $row[] = $product->root_id;
            fputcsv($file, $row);
        }
        fclose($file);
        return $filename;    
    }
    private function get_index(Request $request)
    {
        $response = [];
        $query = Product::where(['is_deleted' => 0]);
        $parentId = $request->query('parent_id');
        $stocks = $request->query('stocks');
        $shops = $request->query('shops');
        $statuses = $request->query('statuses');
        $trademarks = $request->query('trademarks');
        $categories = $request->query('categories');
        $limit = $request->query('limit');
        $export = $request->query('export');
        $report = $request->query('report');
        if (!isset($limit)) $limit = 20;
        $page = $request->query('page');
        if (!$report) {
            $module_id = self::$module_id;
        } else if ($report == 1) {
            $module_id = self::$module_report_stock_id;
        } else if ($report == 2) {
            $module_id = self::$module_report_order_id;
        }
        $allowed_shops = AppController::get_shops_by_module($module_id);
        $allowed_stocks = $report ? AppController::get_stocks_by_module($module_id) : null;
        $allowed_catalogs_for_purchase = AppController::get_rule_catalogs_by_module(self::$module_id);
        if (isset($parentId)) {
            if ($parentId !== 'all') {
                $query->where('catalog_id', $request->query('parent_id'));
            }
            
        } else {
            $query->where('catalog_id', null);
        }
        if ($request->query('search')) {
            $query->where('name', 'like', '%'.$request->query('search').'%');
        }
        if ($limit) {
            $query->take($limit);
            if ($page) {
                $query->skip(($page - 1) * ($limit));
            }
        }
        $query->with('images')->with(['product_stats' => function ($query) use($stocks, $allowed_stocks, $report) {
            if ($stocks) {
                $stocks = explode(',', $stocks);
                $query->whereIn('stock_id', $stocks);
            }
            if ($allowed_stocks && $report) $query->whereIn('stock_id', $allowed_stocks);
        }]);
        if (!$report) {
            $query->where('is_variant', '=', 0);
        } else {
            $query->orderBy('catalog_id');
            $allowed_folders = null;
            if ($allowed_stocks) $allowed_folders = DB::table('stocks_folders')->whereIn('stock_id', $allowed_stocks)->pluck('folder_id');
            $folderQuery = Folder::where(['is_deleted' => 0]);
            if ($allowed_folders) $folderQuery->whereIn('id', $allowed_folders);
            $response['folders'] = $folderQuery->get();
            if ($allowed_folders) $query->whereIn('root_id', $allowed_folders);
            if (!$export) {
                $stocksQuery = Stock::where(['is_deleted' => 0]);
                if ($allowed_stocks) {
                    $stocksQuery->whereIn('id', $allowed_stocks);
                }
                $response['stocks'] = $stocksQuery->get();
                $response['trademarks'] = Trademark::where(['is_deleted' => 0])->get();
            }
            if ($report == 2) {
                if (!$export) {
                    $response['shops'] = Shop::query($module_id)->get();
                    $response['statuses'] = OrderController::getStatuses();
                }
                $query->with(['orders' => function ($query) use($shops, $statuses, $allowed_shops) {
                    if ($allowed_shops) $query->whereIn('shop_id', $allowed_shops);
                    if ($shops) {
                        $shops = explode(',', $shops);
                        $query->whereIn('shop_id', $shops);
                    }
                    if ($statuses) {
                        $statuses = explode(',', $statuses);
                        $query->whereIn('status', $statuses);
                    }
                }]);
                // return $query->get();
            }
            if ($categories) {
                $categories = explode(',', $categories);
                $query->where(function ($query) use($categories) {
                    $query->whereIn('parent_id', $categories);
                    $query->orWhereIn('root_id', $categories);
                });
            }
            
            if (isset($trademarks)) {
                $trademarks = explode(',', $trademarks);
                $with_null = false;
                foreach($trademarks as $trademark) {
                    if ($trademark == '0') $with_null = true;
                }
                $query->where(function ($query) use($trademarks, $with_null) {
                    $query->whereIn('trademark_id', $trademarks);
                    if ($with_null) $query->orWhereNull('trademark_id');
                });
            }
        }
        $data = $query->get();
        if ($report) {
            foreach($data as $key => $product) {
                if (is_array($allowed_catalogs_for_purchase)) {
                    if (!in_array($product->root_id, $allowed_catalogs_for_purchase)) {
                        unset($product['purchase_price']);
                    }
                }
                $product['in_order_count'] = 0;
                $product['in_order_sum'] = 0;
                $product['sold_count'] = 0;
                $product['sold_sum'] = 0;
                $product['sold_purchase_sum'] = 0;
                foreach($product->orders as $order)
                {
                    if ($order->status == 13) {
                        $product['sold_count'] += $order->pivot->count;
                        $product['sold_sum'] = $order->pivot->count * $order->pivot->price;
                        $product['sold_purchase_sum'] = $order->pivot->count * $order->pivot->purchase_price;
                    } else {
                        $to_status = OrderController::getStatuseToStatus($order->status);
                        if ($to_status) {
                            $product[$to_status] = $product[$to_status]  ? $product[$to_status] + $order->pivot->count : $order->pivot->count;
                        }
                        if ($order->status != 15) {
                            $product['in_order_count'] += $order->pivot->count;
                            $product['in_order_sum'] += $order->pivot->count * $order->pivot->price;
                        }
                    }
                }
                unset($product['orders']);
            }
        }
        $response['products'] = $data;
        if ($export) {
            if ($report == 1) {
                $columns = ['Код', 'Наименование', 'Артикул', 'Доп. описание', 'Единиц', 'Себест', 'Единиц', 'Себест', 'Единиц', 'Себест', 'Единиц', 'Цена продажи', 'Мин. кол-во'];
                $headers = ['', '', '', '', 'ОСТАТКИ', '', 'В НАЛИЧИИ', '', 'В РЕЗЕРВЕ', '', 'ПОДГОТОВЛЕНО', '', ''];
            } else {
                $columns = ['Код', 'Наименование', 'Артикул', 'Единиц', 'На сумму', 'В резерве', 'Упакованы', 'В доставке', 'Единиц', 'На сумму', 'Cебест.', 'Прибыль', 'ROS'];
                $headers = ['', '', '', 'В заказах', '', 'Отгружено', '', '', 'Продано', '', '', '', ''];
            }
            $date = date_create();
            $filename = date_timestamp_get($date) . '_product_report_'. $report .'.csv';
            $file = fopen($filename, 'w');
            fputcsv($file, $headers);
            fputcsv($file, $columns);
            if ($report == 1) {
                $folders = [];
                foreach($data as $product) {
                    $product['in_prepare'] = 0;
                    $product['in_stock'] = 0;
                    $product['in_reserve'] = 0;
                    foreach($product->product_stats as $stat) {
                        $product['in_prepare'] += $stat['in_prepare'];
                        $product['in_stock'] += $stat['in_stock'];
                        $product['in_reserve'] += $stat['in_reserve'];
                    }
                }
                foreach($response['folders']->toArray() as $folder) {
                    $folder['products'] = [];
                    foreach($data as $product) {
                        if ($product['catalog_id'] == $folder['id']) {
                            $folder['products'][] = $product;
                        }
                    }
                    $folders[] = $folder;
                }
                foreach($folders as $folder) {
                    if (count($folder['products'])) {
                        fputcsv($file, [$folder['name']]);
                    }
                    foreach($folder['products'] as $product) {
                        fputcsv($file, [$product->id, $product->name, $product->sku, $product->additional_info, $product['in_reserve'] + $product['in_stock'], ($product['in_reserve'] + $product['in_stock']) * $product->purchase_price, $product['in_stock'], $product['in_stock'] * $product->purchase_price, $product['in_reserve'], $product['in_reserve'] * $product->purchase_price, $product['in_prepare'], $product->selling_price, $product->min_count]);
                    }
                }
            } else {
                foreach($data as $row) {
                    $ros = 0; 
                    if ($row->sold_sum) {
                        $ros = ($row->sold_sum - $row->sold_purchase_sum) / $row->sold_sum * 100;
                    }
                    fputcsv($file, [$row->id, $row->name.'('.$row->additional_info.')', $row->sku, $row->in_order_count, $row->in_order_sum, $row->in_reserve, $row->in_package, $row->in_delivery, $row->sold_count, $row->sold_sum, $row->sold_purchase_sum, $row->sold_sum - $row->sold_purchase_sum, $ros]);
                }
            }
            fclose($file);
            return $filename;
        } else {
            $response['total'] = $query->count();
            return $response;
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->get_index($request);
    }
    public function index_report(Request $request)
    {
        return $this->get_index($request);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {
        $is_variation = $request->input('is_variation');
        $is_variant = $request->input('is_variant');
        $product = Product::create($request->all());
        if (!$is_variation) {
            if (isset($request->photos) && count($request->photos)) {
                $files = [];
                $index = 1;
                $image_ids = [];
                foreach($request->photos as $photo)
                {
                    $path = '/storage/'.basename($photo->store('public'));
                    $files[] = $path;
                    $img = Image::create([
                        'url' => $path
                    ]);
                    $image_ids[$img->id] = ['order' => $index];
                    $index++;
                }
                $product->images()->attach($image_ids);
                if (isset($files[0])) {
                    $product['images'] = [
                        0 => ['url' => $files[0]]
                    ];
                }
            }
            if ($is_variant) {
                $attrs = [];
                $attributesArray = json_decode($request->input('attributes'), true);
                $attributesArray = $attributesArray ? $attributesArray : [];
                foreach($attributesArray as $attr)
                {
                    $attrs[$attr['value']] = ['attribute_id' => $attr['attribute_id']];
                }
                $product->attributesValues()->attach($attrs);
                $product->attributesValues;
                return $product;
            } else {
                return $product;
            }
        } else {
            $product->attributes()->attach($request->input('attributes'));
            return $product;
        }
    }
    public function info(Product $product, Request $request)
    {
        return $product->product_stats()->with('stock')->get();
    }
    public function price_groups(Product $product, Request $request)
    {
        $data = [];
        foreach($request->input('data') as $row) {
            $data[$row['id']] = ['value' => isset($row['value'])  ? $row['value'] : 0];
        }
        $product->price_groups()->sync($data);
        return 'ok';
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product, Request $request)
    { 
        if ($product->is_variation) {
            $product->attributes;
            $product['variations'] = $product->variations()->with('attributesValues')->with('images')->get();
        }
        else {
            $allowed_catalogs_for_purchase = AppController::get_rule_catalogs_by_module(self::$module_id);
            if (is_array($allowed_catalogs_for_purchase)) {
                if (!in_array($product->root_id, $allowed_catalogs_for_purchase)) {
                    unset($product->purchase_price);
                }
            }
            $product->images;
            if ($product->is_variant) {
                $product->attributesValues;
                // ----
                $parent = Product::find($product->parent_id);
                $product['product'] = $parent;
                $parent['attributes'] = $parent->attributes()->with('values')->get();
                return [
                    'variation' => $product,
                    'parent_attributes' => $parent['attributes']
                ];
            }
        }
        $product->price_groups;
        $response = ['data' => $product];
        if ($request->query('info')) {
            $response['trademarks'] = Trademark::where(['is_deleted' => 0])->get();
            $response['price_groups'] = PriceGroup::all();
        }
        return $response;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreProductRequest $request, Product $product)
    {
        $product->update($request->all());
        if ($product->is_variant) {
            $attrs = [];
            foreach($request->input('updatedAttributes') as $attr)
            {
                $attrs[$attr['value']] = ['attribute_id' => $attr['attribute_id']];
            }
            $product->attributesValues()->sync($attrs);
        }
        return 'ok';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->update(['is_deleted' => true]);
        return 'ok';
    }
    public function delete_attribute(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:products',
            'attribute_id' => 'required|exists:attributes,id'
        ]);
        $product = Product::find($request->input('id'));
        $product->attributes()->detach($request->input('attribute_id'));
        return 'ok';
    }
    public function add_attribute(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:products',
            'attribute_id' => 'required|exists:attributes,id'
        ]);
        $product = Product::find($request->input('id'));
        $product->attributes()->attach($request->input('attribute_id'));
        return $product->attributes()->latest()->first();
    }
}
