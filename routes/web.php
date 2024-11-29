<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Website\CurrencieController;
use App\Http\Controllers\Website\PriceSymbolController;
// use App\Livewire\PriceSymbols;
use App\Livewire\PriceSymbolsNew;
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

// Route::get('/tradeingliveggggg', [PriceSymbolsNew::class])->name('tradeinglive2');

// Route::get('/tradeinglive', function () { return view('dashboard-live'); })->name('tradeinglive');

// Route::get('/dashboard-live', [PriceSymbolsNew::class])->name('dashboard'); // live



Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', ])->group(function () {


    // Route::get('/dashboard-live', PriceSymbolsNew::class)->name('dashboard'); // live
    Route::get('/', NewPriceSymbols::class)->name('dashboard'); // live
    // Route::get('/dashboard-live', PriceSymbolsNew::class)->name('dashboard'); // live


    // Route::get('/dashboard', [PriceSymbolController::class , 'index'])->name('dashboard');
    // Route::get('/', [PriceSymbolController::class , 'index']);
    Route::get('/tradeing', [CurrencieController::class , 'index'])->name('tradeing');

    // Route::resource('price-symbols', PriceSymbolController::class);

    // Route::get('/api/prices', [PriceSymbolController::class, 'getPrices'])->name('prices.api');
    // Route::post('/api/update-prices', [PriceSymbolController::class, 'updatePrices']);

    // Route::get('/tradeinglive', [PriceSymbols::class])->name('tradeinglive');


});
