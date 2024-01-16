<?php

use App\Http\Controllers\Dashboard\DashboardEstimacionesController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

    Route::get('getQuotesStatus', [DashboardEstimacionesController::class, 'getQuotesStatus'])->name('get.quotes.status');
    Route::get('getQuotesManurity', [DashboardEstimacionesController::class, 'getQuotesManurity'])->name('get.quotes.manurity');
    Route::get('getStatusWeek', [DashboardEstimacionesController::class, 'getStatusWeek'])->name('get.quotes.status.week');
    Route::get('getAvgManurities', [DashboardEstimacionesController::class, 'getAvgManurities'])->name('get.avg.manurities');
    Route::get('getEstimatorData', [DashboardEstimacionesController::class, 'getEstimatorData'])->name('get.estimator.data');
});
