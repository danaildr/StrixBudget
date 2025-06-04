<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ExchangeRateService
{
    private const ECB_API_URL = 'https://open.er-api.com/v6/latest/EUR';

    public function getEuroRate(string $currency): float
    {
        if ($currency === 'EUR') {
            return 1.0;
        }

        Log::info("Getting exchange rate for {$currency}");

        return Cache::remember("exchange_rate_{$currency}", 3600, function () use ($currency) {
            try {
                Log::info("Cache miss for {$currency}, calling API");
                
                $response = Http::get(self::ECB_API_URL);

                Log::info("API request URL: " . $response->effectiveUri());

                if ($response->successful()) {
                    $data = $response->json();
                    Log::info("Exchange rate API response:", [
                        'status' => $response->status(),
                        'data' => $data
                    ]);
                    
                    if (isset($data['rates'][$currency])) {
                        $rate = $data['rates'][$currency];
                        Log::info("Got rate for {$currency}: {$rate}");
                        return $rate;
                    }
                    
                    Log::error("Rate not found in response for {$currency}");
                    return 1.0;
                }

                Log::error("Failed to get exchange rate for {$currency}:", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return 1.0;
            } catch (\Exception $e) {
                Log::error("Error getting exchange rate for {$currency}:", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return 1.0;
            }
        });
    }

    public function convertToEuro(float $amount, string $currency): float
    {
        if ($currency === 'EUR') {
            return $amount;
        }

        $rate = $this->getEuroRate($currency);
        Log::info("Converting {$amount} {$currency} to EUR using rate {$rate}");
        
        // Делим сумата по курса, за да получим стойността в евро
        // Например: 100 BGN / 1.95583 ≈ 51.13 EUR
        $result = $amount / $rate;
        Log::info("Conversion result: {$result} EUR");
        
        return $result;
    }
} 