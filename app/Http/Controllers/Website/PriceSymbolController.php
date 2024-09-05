<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\priceSymbol;
use Illuminate\Http\Request;

class PriceSymbolController extends Controller
{

    public function index()
    {
        $Prices_Symbols = priceSymbol::all();
        return view('dashboard', compact('Prices_Symbols'));
    }


    public function create()
    {
        return view('price-symbols.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'currency_name' => 'required|string|max:255',
            'current_price' => 'required|numeric',
            'average_buy_price' => 'required|numeric',
            'percentage_change' => 'required|numeric',
            'quantity' => 'required|numeric',
            'purchase_amount' => 'required|numeric',
            'current_value' => 'required|numeric',
        ]);

        PriceSymbol::create($request->all());

        return redirect()->route('price-symbols.index')->with('success', 'تمت إضافة العملة بنجاح');
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
            'current_price' => 'required|numeric',
            'average_buy_price' => 'required|numeric',
            'percentage_change' => 'required|numeric',
            'quantity' => 'required|numeric',
            'purchase_amount' => 'required|numeric',
            'current_value' => 'required|numeric',
        ]);

        $priceSymbol = PriceSymbol::findOrFail($id);
        $priceSymbol->update($request->all());

        return redirect()->route('price-symbols.index')->with('success', 'تم تعديل العملة بنجاح');
    }

    public function destroy($id)
    {
        $priceSymbol = PriceSymbol::findOrFail($id);
        $priceSymbol->delete();

        return redirect()->route('price-symbols.index')->with('success', 'تم حذف العملة بنجاح');
    }
}
