<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Folder;
use App\Attribute;
use App\PriceGroup;
use App\Trademark;
use App\Currency;
use App\Http\Requests\StoreFolderRequest;

class FolderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Folder::query()->get();
    }
    public function info(Request $request)
    {
        $response = [
            'folders' => Folder::query()->get(),
            'stats' => [
                'attrs' => Attribute::count(),
                'currencies' => Currency::count(),
                'trademark' => Trademark::count(),
                'price_group' => PriceGroup::count(),
            ]
        ];
        return $response;
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
    public function store(StoreFolderRequest $request)
    {
        return Folder::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Folder $folder)
    {
        return $folder;
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
    public function update(StoreFolderRequest $request, Folder $folder)
    {
        $folder->update($request->all());
        return 'success';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Folder $folder)
    {
        $folder->update(['is_deleted' => true]);
        return 'ok';
    }
}
