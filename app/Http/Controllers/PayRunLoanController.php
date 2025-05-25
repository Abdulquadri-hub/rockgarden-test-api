<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePayRunLoanRequest;
use App\Http\Requests\UpdatePayRunLoanRequest;
use App\Models\PayRunLoan;

class PayRunLoanController extends Controller
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
     * @param  \App\Http\Requests\StorePayRunLoanRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePayRunLoanRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PayRunLoan  $payRunLoan
     * @return \Illuminate\Http\Response
     */
    public function show(PayRunLoan $payRunLoan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PayRunLoan  $payRunLoan
     * @return \Illuminate\Http\Response
     */
    public function edit(PayRunLoan $payRunLoan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePayRunLoanRequest  $request
     * @param  \App\Models\PayRunLoan  $payRunLoan
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePayRunLoanRequest $request, PayRunLoan $payRunLoan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PayRunLoan  $payRunLoan
     * @return \Illuminate\Http\Response
     */
    public function destroy(PayRunLoan $payRunLoan)
    {
        //
    }
}
