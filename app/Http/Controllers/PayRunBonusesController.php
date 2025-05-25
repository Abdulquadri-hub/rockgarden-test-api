<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePayRunBonusesRequest;
use App\Http\Requests\UpdatePayRunBonusesRequest;
use App\Models\PayRunBonuses;

class PayRunBonusesController extends Controller
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
     * @param  \App\Http\Requests\StorePayRunBonusesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePayRunBonusesRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PayRunBonuses  $payRunBonuses
     * @return \Illuminate\Http\Response
     */
    public function show(PayRunBonuses $payRunBonuses)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PayRunBonuses  $payRunBonuses
     * @return \Illuminate\Http\Response
     */
    public function edit(PayRunBonuses $payRunBonuses)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePayRunBonusesRequest  $request
     * @param  \App\Models\PayRunBonuses  $payRunBonuses
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePayRunBonusesRequest $request, PayRunBonuses $payRunBonuses)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PayRunBonuses  $payRunBonuses
     * @return \Illuminate\Http\Response
     */
    public function destroy(PayRunBonuses $payRunBonuses)
    {
        //
    }
}
