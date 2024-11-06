<?php

namespace App\Services;

use App\Models\Conversion;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyConversionService
{
    public function convertCurrency($sourceCurrency, $targetCurrency, $amount)
    {
        try {
            $response = Http::get("http://data.fixer.io/api/latest", [
                'access_key' => env('FIXER_API_KEY'),
                'symbols' => "{$sourceCurrency},{$targetCurrency}"
            ]);

            if ($response->failed() || !$response->json('success')) {
                Log::error('Fixer API error', ['response' => $response->body()]);
                throw new \Exception('Unable to retrieve exchange rates.');
            }

            $rates = $response->json('rates');
            if (!isset($rates[$sourceCurrency]) || !isset($rates[$targetCurrency])) {
                throw new \Exception('Invalid currency codes.');
            }

            $rate = $rates[$targetCurrency] / $rates[$sourceCurrency];

            $convertedAmount = $amount * $rate;

            $this->saveConversion($sourceCurrency, $targetCurrency, $amount, $convertedAmount);

            return [
                'source_currency' => $sourceCurrency,
                'target_currency' => $targetCurrency,
                'amount' => $amount,
                'converted_amount' => $convertedAmount,
                'rate' => $rate,
                'timestamp' => now(),
            ];
        } catch (\Exception $e) {
            Log::error('Currency conversion error', ['exception' => $e->getMessage()]);
            throw new \Exception('An error occurred during currency conversion.');
        }
    }

    private function saveConversion($sourceCurrency, $targetCurrency, $amount, $convertedAmount)
    {
        Conversion::create([
            'source_currency' => $sourceCurrency,
            'target_currency' => $targetCurrency,
            'amount' => $amount,
            'converted_amount' => $convertedAmount,
        ]);
    }
}
