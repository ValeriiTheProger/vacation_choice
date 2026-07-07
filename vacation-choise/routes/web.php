<?php

use App\Http\Controllers\DestinationController;
use App\Http\Controllers\StatisticsController;
use Illuminate\Support\Facades\Route;

Route::prefix('vacation-choise')->group(function () {
    Route::get('/', [DestinationController::class, 'index'])->name('home');

    Route::get('/search/results', [DestinationController::class, 'search'])->name('search.results');

    Route::get('/destinations', [DestinationController::class, 'all'])->name('destinations.index');

    Route::get('/destinations/compare', [DestinationController::class, 'compare'])->name('destinations.compare');

    Route::get('/destinations/{destination:slug}', [DestinationController::class, 'show'])->name('destinations.show');

    Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics.index');
});
