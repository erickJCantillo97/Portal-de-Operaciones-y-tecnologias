<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\Quotes\QuoteController;
use App\Http\Controllers\Quotes\QuoteStatusController;
use App\Http\Controllers\Quotes\QuoteTypeShipController;
use App\Http\Controllers\Quotes\QuoteVersionController;
use Illuminate\Support\Facades\Route;


//CRUD Quotes
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::resource('quotes', QuoteController::class);
    Route::resource('quotestatus', QuoteStatusController::class);
    Route::resource('quotesversion', QuoteVersionController::class)->except(('store'));
    Route::post('quotesversion/store/{quoteVersion}', [QuoteVersionController::class, 'store'])->name('quotesversion.store');
    Route::get('quotesversion/{quote}/updating', [QuoteVersionController::class, 'updating'])->name('quotesversion.updating');
    Route::resource('comment', CommentController::class);
    Route::post('quote_version_ship_update/{quoteVersion}', [QuoteTypeShipController::class, 'update'])->name('quote.version.type_ship.update');
});
