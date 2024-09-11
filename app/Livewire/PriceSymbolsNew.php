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


class PriceSymbolsNew extends Component
{
    public $pricesSymbols;
    public $symbols;
    public $GetPrices;

    // تحديث البيانات كل فترة زمنية باستخدام WebSocket
    protected $listeners = ['priceUpdated' => 'updatePrices'];

    public function mount()
    {
        $this->GetPrices();
    }

    public function GetPrices()
    {
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

    public function updatePrices()
    {
        try {
            // استدعاء API لجلب الأسعار
            $prices = $this->fetchPricesFromApi();

            // بث الأسعار المحدثة عبر WebSocket
            event(new MyEvent('تحديث السعر الجديد'));

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

                    $percentageChange = $averageBuyPrice > 0 ? (($currentPrice - $averageBuyPrice) / $averageBuyPrice) * 100 : 0;
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
            $this->GetPrices();

        } catch (\Exception $e) {
            Log::error("Error updating prices: " . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            // العثور على العملة باستخدام الـ ID وحذفها
            $priceSymbol = PriceSymbol::findOrFail($id);
            $priceSymbol->delete();

            // إعادة تحميل البيانات المحدثة بعد الحذف
            $this->GetPrices();

            // إرجاع رسالة نجاح بعد الحذف
            session()->flash('success', 'تم حذف العملة بنجاح');
        } catch (\Exception $e) {
            // تسجيل الخطأ وإرجاع رسالة خطأ
            Log::error("Error deleting price symbol: " . $e->getMessage());
            session()->flash('error', 'حدث خطأ أثناء محاولة حذف العملة');
        }
    }

    #[Layout('dashboard-live')]
    public function render(): View|Application|Factory
    {
        return view('livewire.price-symbols-new');
    }
}
