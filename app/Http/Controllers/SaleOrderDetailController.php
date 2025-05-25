<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleOrderDetailRequest;
use App\Http\Requests\UpdateSaleOrderDetailRequest;
use App\Models\SaleOrderDetail;

class SaleOrderDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Requests\StoreSaleOrderDetailRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSaleOrderDetailRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SaleOrderDetail  $saleOrderDetail
     * @return \Illuminate\Http\Response
     */
    public function show(SaleOrderDetail $saleOrderDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SaleOrderDetail  $saleOrderDetail
     * @return \Illuminate\Http\Response
     */
    public function edit(SaleOrderDetail $saleOrderDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSaleOrderDetailRequest  $request
     * @param  \App\Models\SaleOrderDetail  $saleOrderDetail
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSaleOrderDetailRequest $request, SaleOrderDetail $saleOrderDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SaleOrderDetail  $saleOrderDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(SaleOrderDetail $saleOrderDetail)
    {
        //
    }
}
