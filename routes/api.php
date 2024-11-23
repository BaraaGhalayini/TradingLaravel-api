<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CurrencieController;
use App\Http\Controllers\Api\LoginController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::get('/', function () {

    return 1;
});

// Route::get('/Currencies', [CurrencieController::class , 'index'])->middleware('auth:sanctum');
// Route::get('/Currencies', [CurrencieController::class , 'index']);

Route::apiResource('Currencies', CurrencieController::class)->middleware('auth:sanctum');

Route::get('/login', [LoginController::class , 'login']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Broadcast::routes(['middleware' => ['auth:api']]);

