<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\priceSymbol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PriceSymbolController extends Controller
{

    public function index()
    {
        $symbols = PriceSymbol::pluck('currency_name')->toArray();

        $Prices_Symbols = priceSymbol::all();

        return view('dashboard', compact('Prices_Symbols' , 'symbols'));
    }

    public function getSymbolDecimals()
    {
        try {
            $response = Http::get('https://api.binance.com/api/v3/exchangeInfo');

            if ($response->successful()) {
                $data = $response->json();
                $symbols = $data['symbols'];

                $decimals = [];
                foreach ($symbols as $symbol) {
                    $symbolName = $symbol['symbol'];
                    $quotePrecision = $symbol['quotePrecision']; // عدد الخانات العشرية للسعر
                    $baseAssetPrecision = $symbol['baseAssetPrecision']; // عدد الخانات العشرية للكمية

                    $decimals[$symbolName] = [
                        'price' => $quotePrecision,
                        'quantity' => $baseAssetPrecision,
                    ];
                }

                return $decimals;
            } else {
                Log::error("Failed to fetch symbol information: " . $response->body());
                return [];
            }
        } catch (\Exception $e) {
            Log::error("Exception occurred while fetching symbol information: " . $e->getMessage());
            return [];
        }
    }

    // للحصول على أسعار العملات من API
    public function getPrices()
    {
        try {
            $prices = $this->fetchPricesFromApi();
            return response()->json($prices);
        } catch (\Exception $e) {
            Log::error("Error fetching prices: " . $e->getMessage());
            return response()->json(['error' => 'Error fetching prices'], 500);
        }
    }

    private function fetchPricesFromApi()
    {
        // الحصول على معلومات التنسيق من Binance
        $decimals = $this->getSymbolDecimals();

        $Price_Symbols = PriceSymbol::pluck('currency_name');
        $prices = [];

        foreach ($Price_Symbols as $symbol) {
            try {
                $formattedSymbol = strtoupper($symbol) . 'USDT';
                $response = Http::get("https://api.binance.com/api/v3/ticker/price?symbol=$formattedSymbol");

                if ($response->failed()) {
                    Log::error("Failed to fetch price for {$formattedSymbol}: " . $response->body());
                    continue;
                }

                $data = $response->json();

                if (isset($data['price'])) {
                    $price = (float) $data['price'];

                    // الحصول على عدد الخانات العشرية
                    $decimalInfo = $decimals[$formattedSymbol] ?? ['price' => 3]; // افتراض 2 إذا لم تتوفر المعلومات

                    // تنسيق السعر بناءً على عدد الخانات العشرية
                    $formattedPrice = number_format($price, $decimalInfo['price'], '.', '');

                    $prices[$formattedSymbol] = $formattedPrice;
                } else {
                    Log::warning("Price not found for {$formattedSymbol}");
                }
            } catch (\Exception $e) {
                Log::error("Exception occurred while fetching price for {$symbol}: " . $e->getMessage());
            }
        }

        return $prices;
    }


    public function updatePrices(Request $request)
    {
        try {
            // الحصول على الأسعار من API
            $prices = $this->fetchPricesFromApi();

            // الحصول على معلومات التنسيق من Binance
            $decimals = $this->getSymbolDecimals();

            // تحديث كل سعر في قاعدة البيانات
            foreach ($prices as $symbol => $price) {
                $currencyName = strtolower(substr($symbol, 0, -4));

                $priceSymbol = PriceSymbol::where('currency_name', $currencyName)->first();

                if ($priceSymbol) {
                    // الحصول على عدد الخانات العشرية
                    $decimalInfo = $decimals[$symbol] ?? ['price' => 3, 'quantity' => 3]; // افتراض 2 إذا لم تتوفر المعلومات

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

                    $priceSymbol->update([
                        'current_price' => $currentPrice,
                        'percentage_change' => $percentageChange,
                        'current_value' => $currentValue,
                        'purchase_amount' => $purchaseAmount,
                    ]);
                }
            }

            return response()->json(['success' => 'Prices updated successfully']);
        } catch (\Exception $e) {
            Log::error("Error updating prices: " . $e->getMessage());
            return response()->json(['error' => 'Error updating prices'], 500);
        }
    }





    public function create()
    {
        return view('price-symbols.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'currency_name' => 'required|string|max:255',
            'current_price' => 'nullable|numeric',
            'average_buy_price' => 'required|numeric',
            'percentage_change' => 'nullable|numeric',
            'quantity' => 'required|numeric',
            'purchase_amount' => 'nullable|numeric',
            'current_value' => 'nullable|numeric',
        ]);

        try {
            // تحويل اسم العملة إلى أحرف كبيرة
            $currencyName = strtoupper($request->input('currency_name'));

            // إعداد البيانات المعدلة
            $data = $request->all();
            $data['currency_name'] = $currencyName;

            // إنشاء سجل جديد باستخدام البيانات المعدلة
            PriceSymbol::create($data);

            return redirect()->route('price-symbols.index')->with('success', 'تمت إضافة العملة بنجاح');
        } catch (\Exception $e) {
            Log::error('Error saving PriceSymbol: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة العملة.');
        }
    }





    public function edit($id)
    {
        $priceSymbol = PriceSymbol::findOrFail($id);
        return view('price-symbols.edit', compact('priceSymbol'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'currency_name' => 'required|string|max:255',
            'current_price' => 'nullable|numeric',
            'average_buy_price' => 'required|numeric',
            'percentage_change' => 'nullable|numeric',
            'quantity' => 'required|numeric',
            'purchase_amount' => 'nullable|numeric',
            'current_value' => 'nullable|numeric',
        ]);

        try {
            $priceSymbol = PriceSymbol::findOrFail($id);

            $currencyName = strtoupper($request->input('currency_name'));

            // $priceSymbol->update(array_merge($request->all(), ['currency_name' => $currencyName]));

            // $priceSymbol->update($request->all());

            $priceSymbol->update([
                'currency_name' =>  $currencyName,
                'average_buy_price' => $request->input('average_buy_price'),
                'quantity' => $request->input('quantity'),
                'purchase_amount' => $request->input('purchase_amount'),
            ]);

            // $priceSymbol::Update ([
            //     'currency_name' => $currencyName,
            //     'current_price' => $request->current_price,
            //     'average_buy_price' => $request->average_buy_price,
            //     'percentage_change' => $request->percentage_change,
            //     'quantity' => $request->quantity ,
            //     'purchase_amount' => $request->purchase_amount,
            //     'current_value' => $request->current_value,
            // ]);

            return response()->json(['success' => 'Price symbol updated successfully']);
            // return redirect()->route('price-symbols.index')->with('success', 'تم تعديل العملة بنجاح');

        } catch (\Exception $e) {
            Log::error('Error updating PriceSymbol: ' . $e->getMessage());
            return response()->json(['error' => 'Error updating PriceSymbol'], 500);
        }
    }

    public function destroy($id)
    {
        $priceSymbol = PriceSymbol::findOrFail($id);
        $priceSymbol->delete();


        return redirect()->route('price-symbols.index')->with('success', 'تم حذف العملة بنجاح');

    }



}
