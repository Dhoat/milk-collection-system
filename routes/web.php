<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VillageController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\MilkCollectionController;
use App\Http\Controllers\MilkReceivingController;
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

    Route::resource('villages', VillageController::class);
    Route::resource('farmers', FarmerController::class);
    Route::resource('milk-collections', MilkCollectionController::class);
    
    // Main Milk Center / Milk Receiving routes
    Route::get('/api/village-collection-summary', [MilkReceivingController::class, 'getCollectionSummary'])
        ->name('api.village-collection-summary');
    Route::resource('milk-receivings', MilkReceivingController::class);
});

require __DIR__.'/auth.php';

