<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PriceSymbol;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Events\MyEvent;
use Livewire\Attributes\Layout;

class NewPriceSymbols extends Component
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

    public function updatePrices()
    {
        $prices = $this->fetchAllPrices();
        // dd($prices);
        foreach ($prices as $currencyName => $price) {
            $priceSymbol = PriceSymbol::where('currency_name', $currencyName)->first();
            if ($priceSymbol) {
                $currentPrice = (float)$price;
                $averageBuyPrice = $priceSymbol->average_buy_price;
                $quantity = $priceSymbol->quantity;

                // العمليات الحسابية
                $percentageChange = ($averageBuyPrice > 0)
                    ? (($currentPrice - $averageBuyPrice) / $averageBuyPrice) * 100
                    : 0;


                // تحديث الحقول في قاعدة البيانات
                $priceSymbol->update([
                    'current_price' => $currentPrice,
                    'percentage_change' => $percentageChange,
                    'current_value' => $quantity * $currentPrice,
                    'purchase_amount' => $quantity * $averageBuyPrice,
                ]);
            }
        }
        event(new MyEvent($prices));
        $this->refreshTotals();

        $this->dispatch('success-message', message: 'تم تحديث الأسعار بنجاح لحظيا ');

    }

    private function fetchAllPrices()
    {
        try {
            // جلب الأسعار من API
            $response = Http::timeout(10)->get("https://api.binance.com/api/v3/ticker/price");
            if ($response->successful()) {
                // جلب أسماء العملات من قاعدة البيانات وتحويلها إلى أحرف كبيرة
                $Price_Symbols = PriceSymbol::pluck('currency_name')->map(fn($symbol) => strtoupper($symbol))->toArray();
                // dd($Price_Symbols);
                if (empty($Price_Symbols)) {
                    Log::error("Price_Symbols is empty. Check database.");
                    return [];
                }

                // تصفية الأزواج المطلوبة فقط
                $filteredPrices = collect($response->json())->filter(function ($priceData) use ($Price_Symbols) {
                    $symbol = $priceData['symbol'];
                    $currency = strtoupper(substr($symbol, 0, -4)); // اسم العملة (الأحرف الكبيرة)
                    $pair = substr($symbol, -4); // الزوج (مثل USDT)
                    return $pair === 'USDT' && in_array($currency, $Price_Symbols);
                })->mapWithKeys(function ($priceData) {
                    $currency = strtoupper(substr($priceData['symbol'], 0, -4)); // تحويل اسم العملة للأحرف الكبيرة
                    return [$currency => (float)$priceData['price']];
                })->toArray();

                // تحقق من النتيجة بعد التصفية
                if (empty($filteredPrices)) {
                    Log::warning("Filtered prices are empty. Check the matching logic.");
                }
                // dd($filteredPrices);
                return $filteredPrices;
            }
        } catch (\Exception $e) {
            Log::error("Error fetching prices: " . $e->getMessage());
            return [];
        }
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
        return view('livewire.NewPriceSymbols');
    }
}
