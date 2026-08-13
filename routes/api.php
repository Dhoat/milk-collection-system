<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DeliveryController;
use App\Http\Controllers\Api\FarmerController;
use App\Http\Controllers\Api\MilkCollectionController;
use App\Http\Controllers\Api\MilkReceivingController;
use App\Http\Controllers\Api\MilkStockController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\ShopOrderController;
use App\Http\Controllers\Api\UserController;
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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management Endpoints (Self-Service for all authenticated users)
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Village & Farmer Collection modules (Accessible by super_admin, manager, and collection_staff)
    Route::middleware('role:super_admin,manager,collection_staff')->group(function () {
        Route::apiResource('villages', VillageController::class);
        Route::apiResource('farmers', FarmerController::class);
        Route::apiResource('milk-collections', MilkCollectionController::class);
    });

    // Milk Receiving, Stock, Shop, Shop Order & Delivery modules (Accessible by super_admin, manager, and center_staff)
    Route::middleware('role:super_admin,manager,center_staff')->group(function () {
        Route::get('milk-receivings/summary', [MilkReceivingController::class, 'getCollectionSummary'])->name('milk-receivings.summary');
        Route::apiResource('milk-receivings', MilkReceivingController::class);

        Route::get('milk-stocks', [MilkStockController::class, 'index'])->name('milk-stocks.index');
        Route::post('milk-stocks/out', [MilkStockController::class, 'storeOut'])->name('milk-stocks.store-out');
        Route::get('milk-stocks/{milkStock}', [MilkStockController::class, 'show'])->name('milk-stocks.show');

        // Shop Management Module
        Route::patch('shops/{shop}/toggle-status', [ShopController::class, 'toggleStatus'])->name('shops.toggle-status');
        Route::apiResource('shops', ShopController::class);

        // Shop Orders Module
        Route::patch('shop-orders/{shopOrder}/status', [ShopOrderController::class, 'updateStatus'])->name('shop-orders.update-status');
        Route::apiResource('shop-orders', ShopOrderController::class)->except(['update']);
        Route::put('shop-orders/{shopOrder}', [ShopOrderController::class, 'updateStatus'])->name('shop-orders.update');
        Route::patch('shop-orders/{shopOrder}', [ShopOrderController::class, 'updateStatus']);

        // Delivery Dispatch Module
        Route::patch('deliveries/{delivery}/status', [DeliveryController::class, 'updateStatus'])->name('deliveries.update-status');
        Route::apiResource('deliveries', DeliveryController::class)->except(['update']);
        Route::put('deliveries/{delivery}', [DeliveryController::class, 'updateStatus'])->name('deliveries.update');
        Route::patch('deliveries/{delivery}', [DeliveryController::class, 'updateStatus']);
    });

    // Management & Executive Reports (Accessible by super_admin and manager only)
    Route::middleware('role:super_admin,manager')->group(function () {
        Route::get('reports/daily', [ReportController::class, 'daily'])->name('reports.daily');
        Route::get('reports/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
    });

    // System Administration & User Management (Accessible by super_admin only)
    Route::middleware('role:super_admin')->group(function () {
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::apiResource('users', UserController::class);
    });
});






