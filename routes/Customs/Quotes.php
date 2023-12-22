<?php

use App\Http\Controllers\Quotes\QuoteController;
use Illuminate\Support\Facades\Route;


//CRUD Quotes
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::resource('quotes', QuoteController::class);
});
