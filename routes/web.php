<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VillageController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\MilkCollectionController;
use App\Http\Controllers\MilkReceivingController;
use App\Http\Controllers\MilkStockController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ShopOrderController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Village, Farmer & Collection modules (Super Admin, Manager, and Collection Staff)
    Route::middleware('role:super_admin,manager,collection_staff')->group(function () {
        Route::resource('villages', VillageController::class);
        Route::resource('farmers', FarmerController::class);
        Route::resource('milk-collections', MilkCollectionController::class);
    });
    
    // Main Milk Center, Milk Stock, Shop, Shop Order & Delivery routes (Super Admin, Manager, and Center Staff)
    Route::middleware('role:super_admin,manager,center_staff')->group(function () {
        Route::get('/api/village-collection-summary', [MilkReceivingController::class, 'getCollectionSummary'])
            ->name('api.village-collection-summary');
        Route::resource('milk-receivings', MilkReceivingController::class);

        Route::get('/milk-stocks', [MilkStockController::class, 'index'])->name('milk-stocks.index');
        Route::post('/milk-stocks/out', [MilkStockController::class, 'storeOut'])->name('milk-stocks.store-out');

        // Shop Management Routes
        Route::patch('/shops/{shop}/toggle-status', [ShopController::class, 'toggleStatus'])->name('shops.toggle-status');
        Route::resource('shops', ShopController::class);

        // Shop Orders Routes
        Route::patch('/shop-orders/{shopOrder}/status', [ShopOrderController::class, 'updateStatus'])->name('shop-orders.update-status');
        Route::resource('shop-orders', ShopOrderController::class);

        // Delivery Routes
        Route::patch('/deliveries/{delivery}/status', [DeliveryController::class, 'updateStatus'])->name('deliveries.update-status');
        Route::resource('deliveries', DeliveryController::class);
    });

    // Management & Executive Reports Routes (Super Admin & Manager only)
    Route::middleware('role:super_admin,manager')->group(function () {
        Route::get('/reports/daily', [ReportController::class, 'daily'])->name('reports.daily');
        Route::get('/reports/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
    });

    // System Administration Routes (Super Admin only)
    Route::middleware('role:super_admin')->group(function () {
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::resource('users', UserController::class);

        // System Settings Routes
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    });
});

require __DIR__.'/auth.php';

