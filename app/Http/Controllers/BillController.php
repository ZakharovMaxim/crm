<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bill;
use App\Payment;
use App\Http\Requests\StoreBillRequest;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Bill::where(['is_deleted' => 0])->withCount('payments')->get();
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
    public function store(StoreBillRequest $request)
    {
        $data = $request->all();
        if (!isset($data['info']) || !$data['info']) $data['info'] = '';
        return Bill::create($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Bill $bill)
    {
        return $bill;
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
    public function update(StoreBillRequest $request, Bill $bill)
    {
        $data = $request->all();
        if ($data['info']) $data['info'] = '';
        $bill->update();
        return 'ok';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bill $bill)
    {
        $bill->is_deleted = true;
        Payment::where(['bill_id' => $bill->id])->update(['is_deleted' => 1]);
        $bill->save();
        return 'ok';
    }
}
