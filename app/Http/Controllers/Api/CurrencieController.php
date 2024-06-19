<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Currencie;
use App\Http\Resources\CurrencieResource;

class CurrencieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Currencie = Currencie::all();
        return CurrencieResource::collection($Currencie);
    }




    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }




    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
