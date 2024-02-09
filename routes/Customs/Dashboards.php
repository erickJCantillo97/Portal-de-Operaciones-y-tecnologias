<?php

use App\Http\Controllers\Dashboard\DashboardEstimacionesController;
use App\Http\Controllers\Dashboard\DashboardProjectsController;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Row;

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

    /* Ofertas */
    Route::get('getQuotesStatus', [DashboardEstimacionesController::class, 'getQuotesStatus'])->name('get.quotes.status');
    Route::get('getQuotesManurity', [DashboardEstimacionesController::class, 'getQuotesManurity'])->name('get.quotes.manurity');
    Route::get('getStatusWeek', [DashboardEstimacionesController::class, 'getStatusWeek'])->name('get.quotes.status.week');
    Route::get('getAvgManurities', [DashboardEstimacionesController::class, 'getAvgManurities'])->name('get.avg.manurities');
    Route::get('getEstimatorData', [DashboardEstimacionesController::class, 'getEstimatorData'])->name('get.estimator.data');
    Route::get('getQuotesCountry', [DashboardEstimacionesController::class, 'getQuotesCountry'])->name('get.quotes.country');
    
    /*  Projects */
    Route::get('projectActive', [DashboardProjectsController::class, 'projectActive'])->name('project.active');
});
