<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Currencie;
use App\Http\Resources\CurrencieResource;
use App\Http\Requests\CurrencieRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
    public function store(CurrencieRequest $request)
    {

        try {
            $currency = Currencie::create($request->validated());

            return response()->json([
                'message' => 'Currency successfully created',
                'data' => CurrencieResource::make($currency),
            ], 201);

        } catch (\Exception $e) {
            // Handle any exceptions that might occur
            return response()->json([
                'error' => 'An error occurred while creating the currency',
                'details' => $e->getMessage(),
            ], 500);
        }
    }





    /**
     * Update the specified resource in storage.
     */
    public function update(CurrencieRequest $request, string $id)
    {
        try {
            $currency = Currencie::findOrFail($id);

            $currency->update([
                'status' => $request->validated()['status'],
            ]);

            // Return a successful response
            return response()->json([
                'message' => 'Currency successfully updated',
                'data' => CurrencieResource::make($currency),
            ], 200);

        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Currency not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $currency = Currencie::findOrFail($id)->delete();

            return response()->json(['message' => 'Successfully Deleted'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Currency not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }
}
