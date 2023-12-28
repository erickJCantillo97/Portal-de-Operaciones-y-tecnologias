<?php

use App\Http\Controllers\Quotes\QuoteController;
use App\Http\Controllers\Quotes\QuoteStatusController;
use App\Http\Controllers\Quotes\QuoteVersionController;
use Illuminate\Support\Facades\Route;


//CRUD Quotes
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::resource('quotes', QuoteController::class);
    Route::resource('quotestatus', QuoteStatusController::class);
    Route::resource('quotesversion', QuoteVersionController::class);
});
