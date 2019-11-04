<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PaymentCategory;
use App\PaymentState;
use App\Payment;
use App\Http\Requests\StorePaymentCategoryRequest;

// type == 1 is income category
// type == 2 is outcome category
class PaymentCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $type = $request->query('type') ? $request->query('type') : '1';
        $parent_id = $request->query('parent_id') ? $request->query('parent_id') : null;
        $data = PaymentCategory::where(['type' => $type, 'is_deleted' => 0, 'parent_id' => $parent_id])->get();
        
        return $data;
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
        return PaymentCategory::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(PaymentCategory $paymentCategory)
    {
        return $paymentCategory;
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
    public function update(StorePaymentCategoryRequest $request, PaymentCategory $paymentCategory)
    {
        $paymentCategory->update($request->all());
        return 'ok';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentCategory $paymentCategory)
    {
        PaymentState::where(['parent_id' => $paymentCategory->id])->update(['is_deleted' => 1]);
        $paymentCategory->is_deleted = true;
        $paymentCategory->save();
        return 'ok';
    }
}
