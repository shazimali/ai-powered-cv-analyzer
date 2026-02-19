<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::prefix('cv-analyzer')->group(function () {
    Route::get('/', [\App\Http\Controllers\CVAnalyzerController::class, 'index'])->name('cv-analyzer.index');
    Route::post('/analyze', [\App\Http\Controllers\CVAnalyzerController::class, 'analyze'])->name('cv-analyzer.analyze');
    Route::get('/status/{uuid}', [\App\Http\Controllers\CVAnalyzerController::class, 'status'])->name('cv-analyzer.status');
});
