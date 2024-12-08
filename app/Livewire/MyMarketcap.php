<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PriceSymbol;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;

class MyMarketcap extends Component
{
    public $pricesSymbols, $sortField = 'current_value', $sortDirection = 'desc' , $testing ;

    public function mount()
    {
        $this->fetchSymbols();
    }

    private function fetchSymbols()
    {
        $this->pricesSymbols = PriceSymbol::orderBy($this->sortField, $this->sortDirection)->get();
    }

    private function fetchAllPrices()
    {
        try {
            $response = Http::timeout(10)->get("https://api.coingecko.com/api/v3/coins/markets", [
                'vs_currency' => 'usd',
                'order' => 'market_cap_desc',
                'per_page' => 250,
                'page' => 1,
                'sparkline' => false,
            ]);

            if ($response->successful()) {
                $priceSymbols = PriceSymbol::pluck('currency_name')->map(fn($symbol) => strtolower($symbol))->toArray();

                return collect($response->json())
                    ->filter(fn($coin) => in_array(strtolower($coin['symbol']), $priceSymbols))
                    ->mapWithKeys(fn($coin) => [
                        strtoupper($coin['symbol']) => [
                            'current_price' => $coin['current_price'],
                            'slug' => $coin['id'],
                            'market_cap_rank' => $coin['market_cap_rank'],
                            'market_cap' => $coin['market_cap'],
                            'volume_24h' => $coin['total_volume'],
                            'price_change_24h' => $coin['price_change_percentage_24h'],
                            'circulating_supply' => $coin['circulating_supply'],
                        ],
                    ])
                    ->toArray();
            }
        } catch (\Exception $e) {
            Log::error("Error fetching prices: " . $e->getMessage());
            return [];
        }
    }

    public function updatePrices()
    {
        $prices = $this->fetchAllPrices();

        foreach ($prices as $currencyName => $data) {
            $priceSymbol = PriceSymbol::where('currency_name', $currencyName)->first();

            if ($priceSymbol) {
                $currentPrice = $data['current_price'];

                // if ($data['test'] == 0){
                $lowestPrice = $this->getLowestPriceIn2024($data['slug']);
                // }
                // else{
                //     $lowestPrice = null;
                // }

                // $percentageChange = ($lowestPrice > 0 && $currentPrice > 0)
                //     ? (($currentPrice / $lowestPrice) * 100) - 100
                //     : 0;

                $priceSymbol->update([
                    'current_price' => $currentPrice,
                    'market_cap_rank' => $data['market_cap_rank'],
                    'slug' => $data['slug'],
                    'market_cap' => $data['market_cap'],
                    'volume_24h' => $data['volume_24h'],
                    'price_change_24h' => $data['price_change_24h'],
                    'lowestPrice' => $lowestPrice,
                    // 'test' => $lowestPrice,
                    'percentage_change_form_low_to_now' => 0,
                ]);
            }
        }

        $this->fetchSymbols();
        $this->dispatch('success-message', message: 'تم تحديث البيانات بنجاح');
    }

    private function getLowestPriceIn2024($coinId)
    {
        $startOf2024 = strtotime('2024-01-01 00:00:00');
        $endOf2024 = strtotime('2024-12-31 23:59:59');

        $response = Http::get("https://api.coingecko.com/api/v3/coins/{$coinId}/market_chart/range", [
            'vs_currency' => 'usd',
            'from' => $startOf2024,
            'to' => $endOf2024,
        ]);

        if ($response->successful()) {
            $prices = $response->json()['prices'];
            return min(array_column($prices, 1));
            // return $prices;
        }



        return 0;
    }

    public function sortBy($field)
    {
        $this->sortDirection = $this->sortField === $field && $this->sortDirection === 'asc' ? 'desc' : 'asc';
        $this->sortField = $field;
        $this->fetchSymbols();
    }


    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.my-marketcap');
    }
}
