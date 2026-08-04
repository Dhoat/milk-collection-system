<?php

namespace App\Http\Controllers;

use App\Models\Farmer;
use App\Models\MilkCollection;
use App\Models\Village;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the milk center admin dashboard.
     */
    public function index(): View
    {
        $today = Carbon::today();
        $todayString = $today->toDateString();

        $totalFarmers = Farmer::count();
        $activeFarmers = Farmer::where('status', true)->count();
        $totalVillages = Village::count();
        $activeVillages = Village::where('status', true)->count();

        $todayQuantity = (float) MilkCollection::whereDate('collection_date', $todayString)->sum('milk_quantity');
        $todayAmount = (float) MilkCollection::whereDate('collection_date', $todayString)->sum('amount');

        $morningQuantity = (float) MilkCollection::whereDate('collection_date', $todayString)
            ->where('shift', 'morning')
            ->sum('milk_quantity');
        $morningFarmerCount = MilkCollection::whereDate('collection_date', $todayString)
            ->where('shift', 'morning')
            ->distinct('farmer_id')
            ->count('farmer_id');

        $eveningQuantity = (float) MilkCollection::whereDate('collection_date', $todayString)
            ->where('shift', 'evening')
            ->sum('milk_quantity');
        $eveningFarmerCount = MilkCollection::whereDate('collection_date', $todayString)
            ->where('shift', 'evening')
            ->distinct('farmer_id')
            ->count('farmer_id');

        $todayFarmerCount = MilkCollection::whereDate('collection_date', $todayString)
            ->distinct('farmer_id')
            ->count('farmer_id');

        $todayOverview = [
            'morning' => [
                'quantity' => $morningQuantity,
                'farmers' => $morningFarmerCount,
            ],
            'evening' => [
                'quantity' => $eveningQuantity,
                'farmers' => $eveningFarmerCount,
            ],
            'total' => [
                'quantity' => $todayQuantity,
                'farmers' => $todayFarmerCount,
            ],
        ];

        $trendStart = $today->copy()->subDays(6);
        $trendTotals = MilkCollection::query()
            ->whereDate('collection_date', '>=', $trendStart->toDateString())
            ->whereDate('collection_date', '<=', $todayString)
            ->selectRaw('collection_date, SUM(milk_quantity) as total_litres')
            ->groupBy('collection_date')
            ->orderBy('collection_date')
            ->pluck('total_litres', 'collection_date');

        $collectionTrend = collect();
        for ($i = 0; $i < 7; $i++) {
            $date = $trendStart->copy()->addDays($i);
            $dateKey = $date->toDateString();
            $collectionTrend->push([
                'date' => $dateKey,
                'label' => $date->format('D'),
                'full_label' => $date->format('M d'),
                'litres' => (float) ($trendTotals[$dateKey] ?? 0),
            ]);
        }

        $trendMax = max($collectionTrend->max('litres'), 1);

        $recentCollections = MilkCollection::query()
            ->with(['farmer.village'])
            ->latest('collection_date')
            ->latest('created_at')
            ->limit(10)
            ->get();

        $villagePerformance = Village::query()
            ->withCount('farmers')
            ->leftJoin('farmers', 'farmers.village_id', '=', 'villages.id')
            ->leftJoin('milk_collections', function ($join) use ($todayString) {
                $join->on('milk_collections.farmer_id', '=', 'farmers.id')
                    ->whereDate('milk_collections.collection_date', $todayString);
            })
            ->select('villages.id', 'villages.name', 'villages.code', 'villages.status')
            ->selectRaw('COALESCE(SUM(milk_collections.milk_quantity), 0) as today_quantity')
            ->selectRaw('COALESCE(SUM(milk_collections.amount), 0) as today_amount')
            ->groupBy('villages.id', 'villages.name', 'villages.code', 'villages.status')
            ->orderByDesc('today_quantity')
            ->orderBy('villages.name')
            ->get();

        $recentActivity = $this->buildRecentActivity();

        return view('dashboard', [
            'today' => $today,
            'kpis' => [
                'total_farmers' => $totalFarmers,
                'active_farmers' => $activeFarmers,
                'total_villages' => $totalVillages,
                'active_villages' => $activeVillages,
                'today_quantity' => $todayQuantity,
                'today_amount' => $todayAmount,
            ],
            'todayOverview' => $todayOverview,
            'collectionTrend' => $collectionTrend,
            'trendMax' => $trendMax,
            'recentCollections' => $recentCollections,
            'villagePerformance' => $villagePerformance,
            'recentActivity' => $recentActivity,
        ]);
    }

    /**
     * Build a merged recent-activity feed from collections, farmers, and villages.
     *
     * @return Collection<int, array{type: string, message: string, timestamp: \Carbon\Carbon, icon: string}>
     */
    private function buildRecentActivity(): Collection
    {
        $activities = collect();

        MilkCollection::query()
            ->with('farmer')
            ->latest('created_at')
            ->limit(8)
            ->get()
            ->each(function (MilkCollection $collection) use ($activities) {
                $farmerName = $collection->farmer?->name ?? __('Unknown Farmer');
                $activities->push([
                    'type' => 'collection',
                    'message' => __('Milk collection recorded for :name', ['name' => $farmerName]),
                    'detail' => number_format($collection->milk_quantity, 2).' L · '.ucfirst($collection->shift),
                    'timestamp' => $collection->created_at,
                    'icon' => 'collection',
                ]);
            });

        Farmer::query()
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->each(function (Farmer $farmer) use ($activities) {
                $activities->push([
                    'type' => 'farmer',
                    'message' => __('New farmer registered: :name', ['name' => $farmer->name]),
                    'detail' => $farmer->farmer_code,
                    'timestamp' => $farmer->created_at,
                    'icon' => 'farmer',
                ]);
            });

        Village::query()
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->each(function (Village $village) use ($activities) {
                $activities->push([
                    'type' => 'village',
                    'message' => __('New village added: :name', ['name' => $village->name]),
                    'detail' => $village->code,
                    'timestamp' => $village->created_at,
                    'icon' => 'village',
                ]);
            });

        return $activities
            ->sortByDesc('timestamp')
            ->take(10)
            ->values();
    }
}
