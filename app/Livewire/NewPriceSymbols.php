<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PriceSymbol;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;
use App\Events\MyEvent;
use Illuminate\Support\Facades\Cache;


class NewPriceSymbols extends Component
{

    public $pricesSymbols;
    public $symbols;
    public $GetPrices;
    public $totalCurrentValue;

    public $newCurrency = [
        'currency_name' => '',
        'average_buy_price' => 0,
        'quantity' => 0,
    ];

    public $editCurrency = [];


    public $deleteId = null; // لتخزين المعرف المراد حذفه


    // تحديث البيانات كل فترة زمنية باستخدام WebSocket
    protected $listeners = [
        'priceUpdated' => 'priceUpdated',
        'resetForm' => 'resetForm',
    ];


    public function mount()
    {
        // $this->mount();
        $this->pricesSymbols = PriceSymbol::all();
        $this->totalCurrentValue = PriceSymbol::sum('current_value');
    }

    public function resetForm()
    {
        $this->reset(['newCurrency', 'editCurrency']);
    }

    public function updatePricesManually()
    {
        $this->updatePrices();
        session()->flash('success', 'تم تحديث الأسعار يدويًا!');
    }


    private function fetchPricesFromApi()
    {
        $decimals = $this->getSymbolDecimals();
        $Price_Symbols = PriceSymbol::pluck('currency_name');
        $prices = [];

        return Cache::remember('prices_data', now()->addMinutes(5), function () use ($Price_Symbols, $decimals) {
            $prices = [];

            foreach ($Price_Symbols as $symbol) {
                $formattedSymbol = strtoupper($symbol) . 'USDT';
                $response = Http::timeout(10)->get("https://api.binance.com/api/v3/ticker/price?symbol=$formattedSymbol");
                if ($response->successful() && isset($response['price'])) {
                    $price = (float) $response['price'];
                    $decimalInfo = $decimals[$formattedSymbol] ?? ['price' => 3];
                    $formattedPrice = number_format($price, $decimalInfo['price'], '.', '');
                    $prices[$formattedSymbol] = $formattedPrice;
                }
            }

            return $prices;
        });


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

    public function priceUpdated($data)
    {
        if (!isset($data) || !is_array($data)) {
            Log::error('Invalid data received for priceUpdated', $data);
            return;
        }

        foreach ($data as $symbol => $price) {
            $currencyName = strtolower(substr($symbol, 0, -4));
            $priceSymbol = PriceSymbol::where('currency_name', $currencyName)->first();

            if ($priceSymbol) {
                $priceSymbol->update(['current_price' => $price]);
            }
        }

        $this->pricesSymbols = PriceSymbol::all(); // تحديث العرض
        $this->totalCurrentValue = PriceSymbol::sum('current_value'); // تحديث القيمة الإجمالية
    }



    public function updateRow($symbol, $price)
    {
        $currency = PriceSymbol::where('currency_name', $symbol)->first();
        if ($currency) {
            $currency->update(['current_price' => $price]);
        }
        $this->pricesSymbols = PriceSymbol::all(); // تحديث البيانات
    }


    public function updatePrices()
    {
        try {
            // استدعاء API لجلب الأسعار
            $prices = $this->fetchPricesFromApi();

            // بث الأسعار المحدثة عبر WebSocket
            // event(new MyEvent('تحديث السعر الجديد'));
            event(new MyEvent($prices)); // $prices يجب أن يكون مصفوفة


            // الحصول على معلومات التنسيق من Binance
            $decimals = $this->getSymbolDecimals();

            // تحديث كل سعر في قاعدة البيانات
            foreach ($prices as $symbol => $price) {
                $currencyName = strtolower(substr($symbol, 0, -4));

                $priceSymbol = PriceSymbol::where('currency_name', $currencyName)->first();

                if ($priceSymbol) {
                    // الحصول على عدد الخانات العشرية
                    $decimalInfo = $decimals[$symbol] ?? ['price' => 3, 'quantity' => 3];

                    $currentPrice = (float) $price;
                    $averageBuyPrice = $priceSymbol->average_buy_price;
                    $quantity = $priceSymbol->quantity;

                    $percentageChange = ($averageBuyPrice > 0) ? (($currentPrice - $averageBuyPrice) / $averageBuyPrice) * 100 : 0;
                    $currentValue = $quantity * $currentPrice;
                    $purchaseAmount = $quantity * $averageBuyPrice;

                    // تنسيق القيم بناءً على عدد الخانات العشرية
                    $currentPrice = number_format($currentPrice, $decimalInfo['price'], '.', '');
                    $percentageChange = number_format($percentageChange, 3, '.', '');
                    $currentValue = number_format($currentValue, $decimalInfo['price'], '.', '');
                    $purchaseAmount = number_format($purchaseAmount, $decimalInfo['price'], '.', '');

                    // تحديث المعلومات في قاعدة البيانات
                    $priceSymbol->update([
                        'current_price' => $currentPrice,
                        'percentage_change' => $percentageChange,
                        'current_value' => $currentValue,
                        'purchase_amount' => $purchaseAmount,
                    ]);
                }
            }

            // إعادة تحميل البيانات المحدثة
            // $this->GetPrices();
        } catch (\Exception $e) {
            Log::error("Error updating prices: " . $e->getMessage());
        }
    }


    // New jobs


    public function broadcastTest()
    {
        $prices = PriceSymbol::pluck('current_price', 'currency_name')->toArray();
        event(new MyEvent($prices));
        session()->flash('success', 'تم بث البيانات بنجاح!');
    }


    public function addCurrency()
    {
        $this->validate([
            'newCurrency.currency_name' => 'required|string|max:255',
            'newCurrency.average_buy_price' => 'required|numeric|min:0',
            'newCurrency.quantity' => 'required|numeric|min:0',
        ]);

        PriceSymbol::create([
            'currency_name' => $this->newCurrency['currency_name'],
            'average_buy_price' => $this->newCurrency['average_buy_price'],
            'quantity' => $this->newCurrency['quantity'],
            'current_price' => 0, // قيمة ابتدائية
            'percentage_change' => 0, // قيمة ابتدائية
            'current_value' => 0, // قيمة ابتدائية
            'purchase_amount' => $this->newCurrency['average_buy_price'] * $this->newCurrency['quantity'],
        ]);

        $this->mount();
        $this->dispatch('close-modal');
        $this->reset('newCurrency');
        session()->flash('success', 'تمت إضافة العملة بنجاح!');
        session()->flash('error', 'حدث خطأ أثناء تحديث البيانات.');
    }

    public function editCurrency($id)
    {
        $currency = PriceSymbol::find($id);
        if ($currency) {
            $this->editCurrency = $currency->toArray(); // تحويل الكائن إلى مصفوفة
        } else {
            session()->flash('error', 'العملة غير موجودة.');
        }
    }


    public function updateCurrency()
    {
        $this->validate([
            'editCurrency.currency_name' => 'required|string|max:255',
            'editCurrency.average_buy_price' => 'required|numeric|min:0',
            'editCurrency.quantity' => 'required|numeric|min:0',
        ]);

        $currency = PriceSymbol::findOrFail($this->editCurrency['id']);
        $purchaseAmount = $this->editCurrency['average_buy_price'] * $this->editCurrency['quantity'];

        $currency->update([
            'currency_name' => $this->editCurrency['currency_name'],
            'average_buy_price' => $this->editCurrency['average_buy_price'],
            'quantity' => $this->editCurrency['quantity'],
            'purchase_amount' => $purchaseAmount,
        ]);

        $this->mount();
        $this->dispatch('close-modal');
        $this->reset('editCurrency'); // إعادة تعيين الخاصية
        session()->flash('success', 'تم تعديل العملة بنجاح!');
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id; // تعيين المعرف
    }

    public function deleteCurrency()
    {
        if ($this->deleteId && $currency = PriceSymbol::find($this->deleteId)) {
            $currency->delete();
            $this->reset('deleteId'); // إعادة تعيين المعرف
            $this->dispatch('close-modal');
            $this->mount();

            session()->flash('success', 'تم حذف العملة بنجاح!');
        } else {
            session()->flash('error', 'العملة غير موجودة.');
        }
    }

    public function closeModal()
    {
        $this->resetForm();
    }


    #[Layout('layouts.app')]
    public function render(): View|Application|Factory
    {
        return view('livewire.NewPriceSymbols');
    }
}
