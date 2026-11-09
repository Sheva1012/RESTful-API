<?php

use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\AssetLoanController;
use App\Http\Controllers\Api\BorrowerController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json([
        'success'   => true,
        'message'   => 'Asset Lending System API is operational',
        'timestamp' => now()->toIso8601String(),
    ]);
});

// Dashboard Statistics
Route::get('/dashboard', [DashboardController::class, 'index']);

// Assets Management
Route::get('/assets/categories', [AssetController::class, 'categories']);
Route::apiResource('assets', AssetController::class);

// Borrowers Management
Route::apiResource('borrowers', BorrowerController::class);

// Loans / Asset Borrowing Management
Route::post('/loans/{id}/return', [AssetLoanController::class, 'returnAsset']);
Route::apiResource('loans', AssetLoanController::class);
