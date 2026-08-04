<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VillageController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\MilkCollectionController;
use App\Models\MilkCollection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $today = Carbon::today()->toDateString();

    $todayStats = [
        'total_quantity' => MilkCollection::whereDate('collection_date', $today)->sum('milk_quantity'),
        'total_amount' => MilkCollection::whereDate('collection_date', $today)->sum('amount'),
        'morning_quantity' => MilkCollection::whereDate('collection_date', $today)->where('shift', 'morning')->sum('milk_quantity'),
        'evening_quantity' => MilkCollection::whereDate('collection_date', $today)->where('shift', 'evening')->sum('milk_quantity'),
    ];

    return view('dashboard', compact('todayStats'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('villages', VillageController::class);
    Route::resource('farmers', FarmerController::class);
    Route::resource('milk-collections', MilkCollectionController::class);
});

require __DIR__.'/auth.php';

