<?php

namespace App\Http\Services;



use Illuminate\Http\Request;

interface SaleOrderServiceInterface
{
    public function createSaleOrder(Request $request);
}
