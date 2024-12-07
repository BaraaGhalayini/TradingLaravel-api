<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Website\CurrencieController;
use App\Http\Controllers\Website\PriceSymbolController;
// use App\Livewire\PriceSymbols;
use App\Livewire\MyMarketcap;
use App\Livewire\NewPriceSymbols;

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

    Route::get('/', NewPriceSymbols::class)->name('dashboard');
    Route::get('/marketcap', MyMarketcap::class)->name('marketcap');
    Route::get('/tradeing', [CurrencieController::class , 'index'])->name('tradeing');

});
