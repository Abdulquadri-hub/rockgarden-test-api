<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceGroupStaffRequest;
use App\Http\Requests\UpdateServiceGroupStaffRequest;
use App\Models\ServiceGroupStaff;

class ServiceGroupStaffController extends Controller
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
     * @param  \App\Http\Requests\StoreServiceGroupStaffRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreServiceGroupStaffRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ServiceGroupStaff  $serviceGroupStaff
     * @return \Illuminate\Http\Response
     */
    public function show(ServiceGroupStaff $serviceGroupStaff)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ServiceGroupStaff  $serviceGroupStaff
     * @return \Illuminate\Http\Response
     */
    public function edit(ServiceGroupStaff $serviceGroupStaff)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateServiceGroupStaffRequest  $request
     * @param  \App\Models\ServiceGroupStaff  $serviceGroupStaff
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateServiceGroupStaffRequest $request, ServiceGroupStaff $serviceGroupStaff)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ServiceGroupStaff  $serviceGroupStaff
     * @return \Illuminate\Http\Response
     */
    public function destroy(ServiceGroupStaff $serviceGroupStaff)
    {
        //
    }
}
