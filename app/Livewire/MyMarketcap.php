<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PriceSymbol;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Events\MyEvent;
use Livewire\Attributes\Layout;

class MyMarketcap extends Component
{

    public $pricesSymbols, $totalCurrentValue, $totalPurchaseAmount, $totalAfterSell;
    public $sortField = 'current_value', $sortDirection = 'desc';
    public $newCurrency = ['currency_name' => '', 'average_buy_price' => 0, 'quantity' => 0];
    public $editCurrency = [];
    public $deleteId;

    protected $listeners = ['priceUpdated' => 'priceUpdated', 'resetForm' => 'resetForm'];


    public function mount()
    {
        $this->refreshTotals();
    }



    // private function fetchAllPrices()
    // {
    //     try {
    //         // استبدل YOUR_API_KEY بمفتاح API الخاص بك
    //         $apiKey = 'YOUR_API_KEY';

    //         $response = Http::withHeaders([
    //             'X-CMC_PRO_API_KEY' => $apiKey,
    //         ])->timeout(10)->get('https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest', [
    //             'start' => 1, // بدءًا من العملة الأولى
    //             'limit' => 250, // عدد العملات المراد جلبها
    //             'convert' => 'USD', // العملة المرجعية
    //         ]);

    //         if ($response->successful()) {
    //             $data = $response->json()['data']; // الحصول على البيانات الأساسية

    //             Log::info($data[0]); // طباعة أول عنصر لتحليل الهيكل
    //             // dd($data[0]); // يمكنك فك التعليق لتجربة الاستجابة

    //             $Price_Symbols = PriceSymbol::pluck('currency_name')->map(fn($symbol) => strtolower($symbol))->toArray();

    //             $filteredPrices = collect($data)->filter(function ($coin) use ($Price_Symbols) {
    //                 return in_array(strtolower($coin['symbol']), $Price_Symbols);
    //             })->mapWithKeys(function ($coin) {
    //                 return [
    //                     strtoupper($coin['symbol']) => [
    //                         'current_price' => $coin['quote']['USD']['price'],
    //                         'market_cap_rank' => $coin['cmc_rank'],
    //                         'market_cap' => $coin['quote']['USD']['market_cap'],
    //                         'volume_24h' => $coin['quote']['USD']['volume_24h'],
    //                         'price_change_24h' => $coin['quote']['USD']['percent_change_24h'],
    //                         'circulating_supply' => $coin['circulating_supply'],
    //                         'sector' => 'غير محدد', // CoinMarketCap لا يقدم معلومات عن القطاع افتراضيًا
    //                     ],
    //                 ];
    //             })->toArray();

    //             return $filteredPrices;
    //         }
    //     } catch (\Exception $e) {
    //         Log::error("Error fetching prices from CoinMarketCap: " . $e->getMessage());
    //         return [];
    //     }
    // }

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
                // $data = $response->json();
                // // طباعة أول عنصر لتحليل الهيكل
                // Log::info($data[0]);

                // dd($data[0]);
                // return $data;

                $Price_Symbols = PriceSymbol::pluck('currency_name')->map(fn($symbol) => strtolower($symbol))->toArray();

                $filteredPrices = collect($response->json())->filter(function ($coin) use ($Price_Symbols) {
                    return in_array(strtolower($coin['symbol']), $Price_Symbols);
                })->mapWithKeys(function ($coin) {
                    return [
                        strtoupper($coin['symbol']) => [
                            'current_price' => $coin['current_price'],
                            'market_cap_rank' => $coin['market_cap_rank'],
                            'market_cap' => $coin['market_cap'],
                            'volume_24h' => $coin['total_volume'],
                            'price_change_24h' => $coin['price_change_percentage_24h'],
                            'circulating_supply' => $coin['circulating_supply'],
                            'sector' => $coin['categories'][0] ?? 'غير محدد', // جلب القطاع
                        ],
                    ];
                })->toArray();

                return $filteredPrices;
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
                $quantity = $priceSymbol->quantity;

                $priceSymbol->update([
                    'current_price' => $currentPrice,
                    'market_cap_rank' => $data['market_cap_rank'],
                    'market_cap' => $data['market_cap'],
                    'volume_24h' => $data['volume_24h'],
                    'price_change_24h' => $data['price_change_24h'],
                    'circulating_supply' => $data['circulating_supply'],
                    'current_value' => $quantity * $currentPrice,
                    'sector' => $data['sector'], // تحديث القطاع

                ]);
            }
        }

        $this->refreshTotals();
        $this->dispatch('success-message', message: 'تم تحديث البيانات بنجاح');
    }

    private function refreshTotals()
    {
        $this->pricesSymbols = PriceSymbol::orderBy($this->sortField, $this->sortDirection)->get();
        $this->totalCurrentValue = PriceSymbol::sum('current_value');
        $this->totalPurchaseAmount = PriceSymbol::sum('purchase_amount');
        $this->totalAfterSell = PriceSymbol::sum('afterSell');
    }

    public function sortBy($field)
    {
        $this->sortDirection = $this->sortField === $field && $this->sortDirection === 'asc' ? 'desc' : 'asc';
        $this->sortField = $field;
        $this->refreshTotals();
    }

    public function getCalculatedValueProperty()
    {
        $averageBuyPrice = $this->newCurrency['average_buy_price'] ?? 0;
        $quantity = $this->newCurrency['quantity'] ?? 0;

        return $averageBuyPrice * $quantity;
    }

    public function resetForm()
    {
        $this->reset(['newCurrency', 'editCurrency']);
    }

    public function addCurrency()
    {
        $this->validate([
            'newCurrency.currency_name' => 'required|string|max:255',
            'newCurrency.average_buy_price' => 'required|numeric',
            'newCurrency.quantity' => 'required|numeric',
        ]);

        PriceSymbol::create([
            'currency_name' => $this->newCurrency['currency_name'],
            'average_buy_price' => $this->newCurrency['average_buy_price'],
            'quantity' => $this->newCurrency['quantity'],
            'purchase_amount' => $this->newCurrency['average_buy_price'] * $this->newCurrency['quantity'],
        ]);

        $this->resetForm();
        $this->refreshTotals();
        $this->dispatch('success-message', message: 'تم إضافة العملة بنجاح!');
    }

    public function editCurrency22($id)
    {
        // Log::debug("editCurrency called with ID: $id");  // سجّل لتتأكد أن الدالة تُستدعى

        $currency = PriceSymbol::find($id);

        if ($currency) {
            $this->editCurrency = $currency->toArray();
        } else {
            $this->dispatch('error-message', message: 'العملة غير موجودة');
        }
    }

    public function updateCurrency()
    {
        $this->validate([
            'editCurrency.currency_name' => 'required|string|max:255',
            'editCurrency.average_buy_price' => 'required|numeric',
            'editCurrency.quantity' => 'required|numeric',
            'editCurrency.target' => 'required|numeric',
        ]);

        $currency = PriceSymbol::findOrFail($this->editCurrency['id']);
        $currency->update([
            'currency_name' => $this->editCurrency['currency_name'],
            'average_buy_price' => $this->editCurrency['average_buy_price'],
            'quantity' => $this->editCurrency['quantity'],
            'purchase_amount' => $this->editCurrency['average_buy_price'] * $this->editCurrency['quantity'],
            'afterSell' => $this->editCurrency['quantity'] * $this->editCurrency['target'],
        ]);

        $this->resetForm();
        $this->refreshTotals();
        $this->dispatch('close-modal');

        $this->dispatch('success-message', message: 'تم تعديل العملة بنجاح!');
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id; // تعيين المعرف
        $this->deleteCurrency();
    }


    public function deleteCurrency()
    {
        if ($this->deleteId && $currency = PriceSymbol::find($this->deleteId)) {
            $currency->delete();
            $this->reset('deleteId');
            $this->refreshTotals();
            $this->dispatch('close-modal');

            $this->dispatch('success-message', message: 'تم حذف العملة بنجاح!');
        } else {
            $this->dispatch('error-message', message: 'العملة غير موجودة');
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.my-marketcap');
    }
}
