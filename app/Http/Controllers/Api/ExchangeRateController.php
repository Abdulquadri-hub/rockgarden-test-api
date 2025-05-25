<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExchangeRate;

class ExchangeRateController extends Controller
{
    public function index()
    {
       $exchangeRates = ExchangeRate::all();

        return response()->json([
            'success' => true,
            'message' => $exchangeRates,
        ]);
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
    public function store(Request $request)
    {
        try {
        // Validate the request data
        $validatedData = $request->validate([
            'currency_base' => 'required|string',
            'currency_quote' => 'required|string',
            'value' => 'required|numeric',
        ]);

        // Create a new exchange rate
        $exchangeRate = ExchangeRate::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => $exchangeRate,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error creating exchange rate.',
        ]);
    }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ExchangeRate  $exchangeRate
     * @return \Illuminate\Http\Response
     */
    public function show(ExchangeRate $exchangeRate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ExchangeRate  $exchangeRate
     * @return \Illuminate\Http\Response
     */
    public function edit(ExchangeRate $exchangeRate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ExchangeRate  $exchangeRate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ExchangeRate $exchangeRate)
    {
        try {
        // Validate the request data
        $validatedData = $request->validate([
            'currency_base' => 'required|string',
            'currency_quote' => 'required|string',
            'value' => 'required|numeric',
        ]);

        // Find the exchange rate by ID
        $exchangeRate = ExchangeRate::findOrFail($request->id);

        // Update the exchange rate
        $exchangeRate->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => $exchangeRate,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid ID or exchange rate not found.',
        ]);
    }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ExchangeRate  $exchangeRate
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, ExchangeRate $exchangeRate)
    {
      try {
        $exchangeRate = ExchangeRate::findOrFail($request->id);
        // Find the exchange rate by ID
        // Delete the exchange rate
        $exchangeRate->delete();

        return response()->json([
            'success' => true,
            'message' => 'Exchange rate deleted successfully.',
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid ID or exchange rate not found.',
        ]);
    }
    }
}
