<?php

namespace App\Http\Controllers;

use App\Services\CurrencyConversionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CurrencyConversionController extends Controller
{
    protected $currencyConversionService;

    public function __construct(CurrencyConversionService $currencyConversionService)
    {
        $this->currencyConversionService = $currencyConversionService;
    }

    public function convert(Request $request)
    {
        $request->validate([
            'source_currency' => 'required|string|size:3',
            'target_currency' => 'required|string|size:3',
            'amount' => 'required|numeric|min:0.01'
        ]);

        $sourceCurrency = strtoupper($request->source_currency);
        $targetCurrency = strtoupper($request->target_currency);
        $amount = $request->amount;

        try {
            $conversionResult = $this->currencyConversionService->convertCurrency(
                $sourceCurrency,
                $targetCurrency,
                $amount
            );

            return response()->json($conversionResult, 200);
        } catch (\Exception $e) {
            Log::error("Currency conversion error: {$e->getMessage()}", [
                'source_currency' => $sourceCurrency,
                'target_currency' => $targetCurrency,
                'amount' => $amount
            ]);
            return response()->json(['error' => 'Unable to retrieve exchange rates.'], 500);
        }
    }
}
