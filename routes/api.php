<?php

use App\Http\Controllers\API\v1\CompetitorController;
use App\Http\Controllers\API\v1\PriceChangeController;
use Illuminate\Support\Facades\Route;

// GP - 19-08-2026 code comment - PriceWatch REST API Web Route mapping (v1)

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // Competitors API CRUD
    Route::get('/competitors', [CompetitorController::class, 'index']);
    Route::post('/competitors', [CompetitorController::class, 'store']);
    Route::get('/competitors/{id}', [CompetitorController::class, 'show']);
    Route::put('/competitors/{id}', [CompetitorController::class, 'update']);
    Route::delete('/competitors/{id}', [CompetitorController::class, 'destroy']);
    
    // Scraper trigger queue check now
    Route::post('/competitors/{id}/check', [CompetitorController::class, 'checkNow']);
    
    // Price changes API log
    Route::get('/changes', [PriceChangeController::class, 'index']);
    
    // Competitor historical analysis
    Route::get('/competitors/{id}/history', [CompetitorController::class, 'priceHistory']);
});
