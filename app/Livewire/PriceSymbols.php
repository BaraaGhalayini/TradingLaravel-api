<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\PriceSymbol;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;

class PriceSymbols extends Component
{
    public $pricesSymbols;
    public $symbols;

    public function mount()
    {
        $this->symbols = PriceSymbol::pluck('currency_name')->toArray();
        $this->pricesSymbols = PriceSymbol::all();
    }

    public function updatePrices()
    {
        // استدعاء API لجلب الأسعار وتحديثها
        $prices = $this->fetchPricesFromApi();

        foreach ($prices as $symbol => $price) {
            $currencyName = strtolower(substr($symbol, 0, -4));
            $priceSymbol = PriceSymbol::where('currency_name', $currencyName)->first();
            if ($priceSymbol) {
                $priceSymbol->update(['current_price' => $price]);
            }
        }

        // إعادة تحميل البيانات المحدثة
        $this->pricesSymbols = PriceSymbol::all();
    }

    private function fetchPricesFromApi()
    {
        $decimals = $this->getSymbolDecimals();
        $Price_Symbols = PriceSymbol::pluck('currency_name');
        $prices = [];

        foreach ($Price_Symbols as $symbol) {
            $formattedSymbol = strtoupper($symbol) . 'USDT';
            $response = Http::get("https://api.binance.com/api/v3/ticker/price?symbol=$formattedSymbol");
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['price'])) {
                    $price = (float) $data['price'];
                    $decimalInfo = $decimals[$formattedSymbol] ?? ['price' => 3];
                    $formattedPrice = number_format($price, $decimalInfo['price'], '.', '');
                    $prices[$formattedSymbol] = $formattedPrice;
                }
            }
        }

        return $prices;
    }

    private function getSymbolDecimals()
    {
        $response = Http::get('https://api.binance.com/api/v3/exchangeInfo');
        $data = $response->json();
        $symbols = $data['symbols'];
        $decimals = [];
        foreach ($symbols as $symbol) {
            $decimals[$symbol['symbol']] = [
                'price' => $symbol['quotePrecision'],
                'quantity' => $symbol['baseAssetPrecision'],
            ];
        }
        return $decimals;
    }

    #[Layout('dashboard-live')]
    public function render(): View|Application|Factory
    {
        return view('livewire.price-symbols');

    }
}

