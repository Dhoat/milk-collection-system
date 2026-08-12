<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FarmerController;
use App\Http\Controllers\Api\MilkCollectionController;
use App\Http\Controllers\Api\MilkReceivingController;
use App\Http\Controllers\Api\VillageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| REST API endpoints for mobile/Flutter application integration.
|
*/

// Public Authentication Endpoints
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// Protected API Routes
Route::middleware('auth:sanctum')->name('api.')->group(function () {
    Route::get('/me', [AuthController::class, 'me'])->name('me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Village & Farmer Collection modules (Accessible by super_admin, manager, and collection_staff)
    Route::middleware('role:super_admin,manager,collection_staff')->group(function () {
        Route::apiResource('villages', VillageController::class);
        Route::apiResource('farmers', FarmerController::class);
        Route::apiResource('milk-collections', MilkCollectionController::class);
    });

    // Milk Receiving Center module (Accessible by super_admin, manager, and center_staff)
    Route::middleware('role:super_admin,manager,center_staff')->group(function () {
        Route::get('milk-receivings/summary', [MilkReceivingController::class, 'getCollectionSummary'])->name('milk-receivings.summary');
        Route::apiResource('milk-receivings', MilkReceivingController::class);
    });
});
