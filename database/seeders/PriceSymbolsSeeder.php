<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PriceSymbolsSeeder extends Seeder
{
    public function run()
    {
        // بيانات العملات التي ترغب بإضافتها
        $priceSymbols = [
            ['currency_name' => 'AVAX', 'average_buy_price' => 50000.00, 'quantity' => 26.897],
            ['currency_name' => 'OP', 'average_buy_price' => 3000.00, 'quantity' => 412.587],
            ['currency_name' => 'DOT', 'average_buy_price' => 1.50, 'quantity' => 106.17372],
            ['currency_name' => 'MANA', 'average_buy_price' => 1.50, 'quantity' => 1602.4959],
            ['currency_name' => 'KDA', 'average_buy_price' => 1.50, 'quantity' => 799.56962],
            ['currency_name' => 'TON', 'average_buy_price' => 1.50, 'quantity' => 81.15876],
            ['currency_name' => 'SUI', 'average_buy_price' => 1.50, 'quantity' => 468.3036],
            ['currency_name' => 'APT', 'average_buy_price' => 1.50, 'quantity' => 39.27568],
            ['currency_name' => 'AVA', 'average_buy_price' => 1.50, 'quantity' => 443.556],
            ['currency_name' => 'NEAR', 'average_buy_price' => 1.50, 'quantity' => 48.5514],
            ['currency_name' => 'RVN', 'average_buy_price' => 1.50, 'quantity' => 10937.9511],
            ['currency_name' => 'IMX', 'average_buy_price' => 1.50, 'quantity' => 144.885],
            ['currency_name' => 'TWT', 'average_buy_price' => 1.50, 'quantity' => 159.87],
            ['currency_name' => 'ATOM', 'average_buy_price' => 1.50, 'quantity' => 22.0779],
            ['currency_name' => 'RENDER', 'average_buy_price' => 1.50, 'quantity' => 16.983],
            ['currency_name' => 'DOGS', 'average_buy_price' => 1.50, 'quantity' => 33079],

            // أضف المزيد من البيانات حسب الحاجة
        ];

        foreach ($priceSymbols as $priceSymbol) {
            DB::table('price_symbols')->updateOrInsert(
                ['currency_name' => $priceSymbol['currency_name']],
                [
                    'average_buy_price' => $priceSymbol['average_buy_price'],
                    'quantity' => $priceSymbol['quantity'],
                    'current_price' => null, // يمكن ترك السعر الحالي كـ null إذا كنت لا تعرفه بعد
                    'percentage_change' => null, // يمكن ترك نسبة التغير كـ null إذا كنت لا تعرفها بعد
                    'purchase_amount' => null, // يمكن ترك مبلغ الشراء كـ null إذا كنت لا تعرفه بعد
                    'current_value' => null // يمكن ترك قيمة المبلغ الآن كـ null إذا كنت لا تعرفها بعد
                ]
            );
        }
    }
}
