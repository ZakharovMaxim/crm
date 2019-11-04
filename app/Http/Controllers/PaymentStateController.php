<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PaymentState;
use App\Payment;
use App\Shop;
use App\Bill;
use App\Http\Requests\StorePaymentCategoryRequest;
use App\Http\Controllers\AppController;
use App\Http\Controllers\PaymentController;

// type == 1 is income category
// type == 2 is outcome category
class PaymentStateController extends Controller
{
    static private $module_id = 5;
    static private $module_id_report = 11;
    private function get_index(Request $request)
    {
        $response = [];
        $type = $request->query('type') ? $request->query('type') : '1';
        $parent_id = $request->query('parent_id') ? $request->query('parent_id') : null;
        $report = $request->query('report');
        $export = $request->query('export');
        $shops = $request->query('shops');
        $bills = $request->query('bills');
        $module_id = $report ? self::$module_id_report : self::$module_id;
        $shops_ids = AppController::get_shops_by_module($module_id);
        $query = PaymentState::where(['type' => $type, 'is_deleted' => 0]);
        if (!$report) {
           $query->where('parent_id', '=', $parent_id); 
        }
        $data = $query->get();
        if ($report) {
            $categories = AppController::get_payment_states_shops(PaymentController::$module_id);
            $statQuery = Payment::where(['is_deleted' => 0])->groupBy('payment_category_id');
            if ($categories) {
                $statQuery->where(function ($query) use($categories) {
                    foreach($categories as $shop_id => $cats)
                    {
                        $query->orWhere(function ($query) use($shop_id, $cats) {
                            $query->where('shop_id', '=', $shop_id);
                            $query->whereIn('payment_category_id', $cats);
                        });
                    }
                });
            }
            if ($shops_ids) {
                $statQuery->whereIn('shop_id', $shops_ids);
            }
            if ($shops) {
                $shops_ids = explode(',', $shops);
                $statQuery->whereIn('shop_id', $shops_ids);
            }
            if ($bills) {
                $bills_ids = explode(',', $bills);
                $statQuery->whereIn('bill_id', $bills_ids);
            }
            $stats = $statQuery
                ->selectRaw('sum(`sum`) as sum, payment_category_id')
                ->pluck('sum','payment_category_id');
            foreach($data as $row)
            {
                $row['sum'] = isset($stats[$row['id']]) ? $stats[$row['id']] : 0;
            }
            if (!$export) {
                $response['shops'] = Shop::where(['is_deleted' => 0])->get();
                $response['bills'] = Bill::where(['is_deleted' => 0])->get();
            }
        }
        if ($export) {
            $columns = ['Категория', 'Процент', 'Сумма'];
            $date = date_create();
            $filename = date_timestamp_get($date) . '_payment_category_report.csv';
            $file = fopen($filename, 'w');
            fputcsv($file, $columns);
            
            $sum = 0;
            foreach($data as $row) {
                $sum += $row->sum;
            }
            foreach($data as $row) {
                if ($sum) {
                    fputcsv($file, [$row->name, ($row->sum / $sum * 100).'%', $row->sum.'UAH']);
                } else {
                    fputcsv($file, [$row->name, 0, 0]);

                }
            }
            fclose($file);
            return $filename;
        } else {
            $response['data'] = $data;
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePaymentCategoryRequest $request)
    {
        return PaymentState::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(PaymentState $paymentState)
    {
        return $paymentState;
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
    public function update(StorePaymentCategoryRequest $request, PaymentState $paymentState)
    {
        $paymentState->update($request->all());
        return 'ok';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentState $paymentState)
    {
        if ($paymentState->id == 1) return 'nonono dont touch my darling';
        $paymentState->is_deleted = true;
        Payment::where(['payment_category_id' => $paymentState->id])->update(['is_deleted' => 1]);
        $paymentState->save();
        return 'ok';
    }
}
