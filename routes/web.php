<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Website\CurrencieController;
use App\Http\Controllers\Website\PriceSymbolController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', ])->group(function () {

    Route::get('/dashboard', [PriceSymbolController::class , 'index'])->name('dashboard');
    Route::get('/', [PriceSymbolController::class , 'index']);
    Route::get('/tradeing', [CurrencieController::class , 'index'])->name('tradeing');

    Route::resource('price-symbols', PriceSymbolController::class);


});
