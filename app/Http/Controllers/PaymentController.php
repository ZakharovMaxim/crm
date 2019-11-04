<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Payment;
use App\PaymentState;
use App\PaymentCategory;
use App\Shop;
use App\Bill;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Controllers\AppController;

class PaymentController extends Controller
{
    static public $module_id = 5;
    static public $module_id_report = 10;
    private function get_index(Request $request)
    {
        $query = Payment::where(['is_deleted' => 0]);
        $per_page = $request->query('per_page') ? $request->query('per_page') : 20;
        $page = $request->query('page') ? $request->query('page') : 1;
        $types = $request->query('types') ? $request->query('types') : '1,2';
        $types = explode(',', $types);
        $bills = $request->query('bills') ? $request->query('bills') : '';
        $shops = $request->query('shops') ? $request->query('shops') : '';
        $categories = $request->query('categories') ? $request->query('categories') : '';
        $sort = $request->query('orderBy') ? $request->query('orderBy') : 'date';
        $dateStart = $request->query('date_from');
        $dateEnd = $request->query('date_to');
        $direction = $request->query('direction') ? $request->query('direction') : 'DESC';
        $report = $request->query('report');
        $export = $request->query('export');
        $init = $request->query('init');
        $module_id = $report ? self::$module_id_report : self::$module_id;
        $allowed_shops_ids = AppController::get_shops_by_module($module_id);
        if ($allowed_shops_ids) $query->whereIn('shop_id', $allowed_shops_ids);
        if ($bills) {
            $bills = explode(',', $bills);
            $query->whereIn('bill_id', $bills);
        }
        if ($shops) {
            $shops = explode(',', $shops);
            $query->whereIn('shop_id', $shops);
        }
        if ($categories) {
            $categories = explode(',', $categories);
            $query->whereIn('payment_category_id', $categories);
        }
        $categories = AppController::get_payment_states_shops(self::$module_id);
        if ($categories) {
            $query->where(function ($query) use($categories) {
                foreach($categories as $shop_id => $cats)
                {
                    $query->orWhere(function ($query) use($shop_id, $cats) {
                        $query->where('shop_id', '=', $shop_id);
                        $query->whereIn('payment_category_id', $cats);
                    });
                }
            });
        }
        if ($dateStart) {
            $query->whereDate('date', '>=', $dateStart);
        }
        if ($dateEnd) {
            $query->whereDate('date', '<=', $dateEnd);
        }
        $query->whereIn('type', $types);
        $statsQuery = clone $query;
        $statsDB = $statsQuery->groupBy('type')
        ->selectRaw('sum(`sum`) as sum, type')
        ->pluck('sum','type');
        $stats['total'] = $query->count();
        if (isset($statsDB['1'])) $stats['income'] = $statsDB['1'];
        if (isset($statsDB['2'])) $stats['outcome'] = $statsDB['2'];
        $query->take($per_page)->skip(($page - 1) * $per_page)->orderBy($sort, $direction);
        $response = [];
        if (($init || $report) && !$export) {
            $categories = PaymentState::where(['is_deleted' => 0])->get();
            $shops = Shop::query($module_id)->get();
            $shops->each(function($shop) use($categories) {
                $shop['categories'] = $shop->getPaymentCategories($categories, self::$module_id);
            });
            // $response['categories'] = $categories;
            $response['shops'] = $shops;
        }
        if ($page == 1 && !$export) {
            $response['stats'] = $stats;
        }
        if ($report) {
            if (!$export) $response['bills'] = Bill::where(['is_deleted' => 0])->get();
            $query->with('shop');
        }
        $response['items'] = $query->with('category')->with('bill')->get();
        if ($export) {
            $columns = ['Дата создания', 'Платеж', 'Категория', 'Сумма', 'Валюта', 'Счёт', 'Магазин', 'Объект', 'Комментарий'];
            $date = date_create();
            $filename = date_timestamp_get($date) . '_payments_report.csv';
            $file = fopen($filename, 'w');
            fputcsv($file, $columns);
            
            foreach($response['items'] as $row) {
                fputcsv($file, [$row->date, 'Платеж #'.$row->id, $row->category->name, ($row->type == 1 ? '+' : '-').$row->sum, 'UAH', $row->bill->name, $row->shop->name, $row->order_id ? 'Заказ #'.$row->order_id : '', $row->comment]);
            }
            fclose($file);
            return $filename;
        } else {
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
    public function create(Request $request)
    {
        $response = [];
        $response['bills'] = Bill::where(['is_deleted' => 0])->get();
        $response['shops'] = Shop::query(self::$module_id)->get();
        $categories = PaymentState::where(['is_deleted' => 0])->get();
        $response['shops']->each(function($shop) use($categories) {
            $shop['categories'] = $shop->getPaymentCategories($categories, self::$module_id);
        });
        return $response;
    }
    public function info(Request $request)
    {
        $response = [];
        $response['categories'] = PaymentCategory::where(['is_deleted' => 0])->get();
        $response['bills'] = Bill::withCount(['payments'])->get();
        return $response;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePaymentRequest $request)
    {
        $payment = Payment::create($request->all());
        $payment->category;
        $payment->bill;
        return $payment;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Payment $payment)
    {
        return $payment;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Payment $payment)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StorePaymentRequest $request, Payment $payment)
    {
        $payment->update($request->all());
        return 'ok';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payment $payment)
    {
        $payment->is_deleted = true;
        $payment->save();
        return 'ok';
    }
}
