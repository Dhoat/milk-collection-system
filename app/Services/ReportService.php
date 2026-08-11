<?php

namespace App\Services;

use App\Models\Delivery;
use App\Models\MilkCollection;
use App\Models\MilkReceiving;
use App\Models\MilkStock;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Generate comprehensive Daily Report data for a specific date and optional filters.
     */
    public function getDailyReport(string $date, array $filters = []): array
    {
        $targetDate = Carbon::parse($date)->toDateString();

        // 1. Milk Collections Aggregation
        $collectionsQuery = MilkCollection::whereDate('collection_date', $targetDate)
            ->when($filters['village_id'] ?? null, fn($q, $v) => $q->whereHas('farmer', fn($fq) => $fq->where('village_id', $v)));

        $totalCollectedLitres = (float) (clone $collectionsQuery)->sum('milk_quantity');
        $farmersCount = (int) (clone $collectionsQuery)->distinct('farmer_id')->count('farmer_id');
        $avgMilkPerFarmer = $farmersCount > 0 ? round($totalCollectedLitres / $farmersCount, 2) : 0.00;
        $totalCollectionAmount = (float) (clone $collectionsQuery)->sum('amount');
        $avgFat = (float) round((clone $collectionsQuery)->avg('fat') ?? 0, 2);
        $avgSnf = (float) round((clone $collectionsQuery)->avg('snf') ?? 0, 2);

        $villageCollections = MilkCollection::join('farmers', 'farmers.id', '=', 'milk_collections.farmer_id')
            ->join('villages', 'villages.id', '=', 'farmers.village_id')
            ->whereDate('milk_collections.collection_date', $targetDate)
            ->when($filters['village_id'] ?? null, fn($q, $v) => $q->where('farmers.village_id', $v))
            ->select(
                'farmers.village_id',
                'villages.name as village_name',
                DB::raw('SUM(milk_collections.milk_quantity) as total_litres'),
                DB::raw('SUM(milk_collections.amount) as total_amount'),
                DB::raw('COUNT(DISTINCT milk_collections.farmer_id) as farmers_count')
            )
            ->groupBy('farmers.village_id', 'villages.name')
            ->get();

        // 2. Main Milk Center Receiving Aggregation
        $receivingsQuery = MilkReceiving::whereDate('receiving_date', $targetDate)
            ->when($filters['village_id'] ?? null, fn($q, $v) => $q->where('village_id', $v));

        $totalReceivedLitres = (float) $receivingsQuery->where('status', 'confirmed')->sum('received_quantity');
        $receivingRecordsCount = (int) $receivingsQuery->count();
        $receivingDiffLitres = round($totalReceivedLitres - $totalCollectedLitres, 2);

        // 3. Milk Stock Ledger Aggregation
        $openingStock = MilkStock::getOpeningStock($targetDate);
        $stockIn = (float) MilkStock::where('type', 'in')->whereDate('transaction_date', $targetDate)->sum('quantity');
        $stockOut = (float) MilkStock::where('type', 'out')->whereDate('transaction_date', $targetDate)->sum('quantity');
        $closingStock = round($openingStock + $stockIn - $stockOut, 2);

        // 4. Shop Orders Aggregation
        $ordersQuery = ShopOrder::whereDate('order_date', $targetDate)
            ->when($filters['shop_id'] ?? null, fn($q, $s) => $q->where('shop_id', $s));

        $ordersCount = (int) $ordersQuery->count();
        $totalOrderValue = (float) $ordersQuery->where('status', '!=', 'cancelled')->sum('total_amount');

        $ordersByStatus = [
            'pending' => (int) (clone $ordersQuery)->where('status', 'pending')->count(),
            'confirmed' => (int) (clone $ordersQuery)->where('status', 'confirmed')->count(),
            'preparing' => (int) (clone $ordersQuery)->where('status', 'preparing')->count(),
            'dispatched' => (int) (clone $ordersQuery)->where('status', 'dispatched')->count(),
            'delivered' => (int) (clone $ordersQuery)->where('status', 'delivered')->count(),
            'cancelled' => (int) (clone $ordersQuery)->where('status', 'cancelled')->count(),
        ];

        // 5. Delivery Dispatches Aggregation
        $deliveriesQuery = Delivery::whereDate('delivery_date', $targetDate)
            ->when($filters['shop_id'] ?? null, fn($q, $s) => $q->where('shop_id', $s));

        $deliveriesCount = (int) $deliveriesQuery->count();
        $deliveriesByStatus = [
            'pending' => (int) (clone $deliveriesQuery)->where('status', 'pending')->count(),
            'assigned' => (int) (clone $deliveriesQuery)->where('status', 'assigned')->count(),
            'out_for_delivery' => (int) (clone $deliveriesQuery)->where('status', 'out_for_delivery')->count(),
            'delivered' => (int) (clone $deliveriesQuery)->where('status', 'delivered')->count(),
            'failed' => (int) (clone $deliveriesQuery)->where('status', 'failed')->count(),
            'cancelled' => (int) (clone $deliveriesQuery)->where('status', 'cancelled')->count(),
        ];

        // 6. Products Sold Aggregation
        $productsSoldQuery = ShopOrderItem::whereHas('order', function ($q) use ($targetDate, $filters) {
            $q->whereDate('order_date', $targetDate)
              ->where('status', '!=', 'cancelled')
              ->when($filters['shop_id'] ?? null, fn($sq, $s) => $sq->where('shop_id', $s));
        })->when($filters['product_id'] ?? null, fn($q, $p) => $q->where('product_id', $p));

        $totalProductsSoldUnits = (float) $productsSoldQuery->sum('quantity');

        $topSellingProducts = ShopOrderItem::whereHas('order', function ($q) use ($targetDate, $filters) {
            $q->whereDate('order_date', $targetDate)
              ->where('status', '!=', 'cancelled')
              ->when($filters['shop_id'] ?? null, fn($sq, $s) => $sq->where('shop_id', $s));
        })->when($filters['product_id'] ?? null, fn($q, $p) => $q->where('product_id', $p))
          ->select('product_id', 'product_name', 'unit', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(line_total) as total_sales'))
          ->groupBy('product_id', 'product_name', 'unit')
          ->orderByDesc('total_qty')
          ->get();

        return [
            'date' => $targetDate,
            'collection' => [
                'farmers_count' => $farmersCount,
                'total_litres' => $totalCollectedLitres,
                'avg_per_farmer' => $avgMilkPerFarmer,
                'total_amount' => $totalCollectionAmount,
                'avg_fat' => $avgFat,
                'avg_snf' => $avgSnf,
                'village_breakdown' => $villageCollections,
            ],
            'center' => [
                'total_received' => $totalReceivedLitres,
                'records_count' => $receivingRecordsCount,
                'diff_litres' => $receivingDiffLitres,
            ],
            'stock' => [
                'opening' => $openingStock,
                'in' => $stockIn,
                'out' => $stockOut,
                'closing' => $closingStock,
            ],
            'orders' => [
                'total_count' => $ordersCount,
                'total_value' => $totalOrderValue,
                'by_status' => $ordersByStatus,
            ],
            'deliveries' => [
                'total_count' => $deliveriesCount,
                'by_status' => $deliveriesByStatus,
            ],
            'products' => [
                'total_units' => $totalProductsSoldUnits,
                'items' => $topSellingProducts,
            ],
            'financial' => [
                'total_sales' => $totalOrderValue,
                'milk_expense' => $totalCollectionAmount,
                'net_balance' => round($totalOrderValue - $totalCollectionAmount, 2),
            ],
        ];
    }

    /**
     * Generate comprehensive Monthly Report data for a specific month/year and optional filters.
     */
    public function getMonthlyReport(int $month, int $year, array $filters = []): array
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

        // 1. Monthly Milk Collections
        $collectionsQuery = MilkCollection::whereBetween('collection_date', [$startDate, $endDate])
            ->when($filters['village_id'] ?? null, fn($q, $v) => $q->whereHas('farmer', fn($fq) => $fq->where('village_id', $v)));

        $totalCollectedLitres = (float) (clone $collectionsQuery)->sum('milk_quantity');
        $farmersCount = (int) (clone $collectionsQuery)->distinct('farmer_id')->count('farmer_id');
        $avgDailyCollection = round($totalCollectedLitres / max(1, $daysInMonth), 2);
        $totalCollectionAmount = (float) (clone $collectionsQuery)->sum('amount');
        $avgFat = (float) round((clone $collectionsQuery)->avg('fat') ?? 0, 2);
        $avgSnf = (float) round((clone $collectionsQuery)->avg('snf') ?? 0, 2);

        $villageCollections = MilkCollection::join('farmers', 'farmers.id', '=', 'milk_collections.farmer_id')
            ->join('villages', 'villages.id', '=', 'farmers.village_id')
            ->whereBetween('milk_collections.collection_date', [$startDate, $endDate])
            ->when($filters['village_id'] ?? null, fn($q, $v) => $q->where('farmers.village_id', $v))
            ->select(
                'farmers.village_id',
                'villages.name as village_name',
                DB::raw('SUM(milk_collections.milk_quantity) as total_litres'),
                DB::raw('SUM(milk_collections.amount) as total_amount'),
                DB::raw('COUNT(DISTINCT milk_collections.farmer_id) as farmers_count')
            )
            ->groupBy('farmers.village_id', 'villages.name')
            ->get();

        // 2. Monthly Main Milk Center
        $receivingsQuery = MilkReceiving::whereBetween('receiving_date', [$startDate, $endDate])
            ->when($filters['village_id'] ?? null, fn($q, $v) => $q->where('village_id', $v));

        $totalReceivedLitres = (float) $receivingsQuery->where('status', 'confirmed')->sum('received_quantity');
        $avgDailyReceiving = round($totalReceivedLitres / max(1, $daysInMonth), 2);

        // Monthly Stock Ledger
        $openingStock = MilkStock::getOpeningStock($startDate);
        $monthlyIn = (float) MilkStock::where('type', 'in')->whereBetween('transaction_date', [$startDate, $endDate])->sum('quantity');
        $monthlyOut = (float) MilkStock::where('type', 'out')->whereBetween('transaction_date', [$startDate, $endDate])->sum('quantity');
        $closingStock = round($openingStock + $monthlyIn - $monthlyOut, 2);

        // 3. Monthly Shop Orders
        $ordersQuery = ShopOrder::whereBetween('order_date', [$startDate, $endDate])
            ->when($filters['shop_id'] ?? null, fn($q, $s) => $q->where('shop_id', $s));

        $ordersCount = (int) $ordersQuery->count();
        $totalOrderValue = (float) $ordersQuery->where('status', '!=', 'cancelled')->sum('total_amount');

        $ordersByStatus = [
            'delivered' => (int) (clone $ordersQuery)->where('status', 'delivered')->count(),
            'pending' => (int) (clone $ordersQuery)->where('status', 'pending')->count(),
            'cancelled' => (int) (clone $ordersQuery)->where('status', 'cancelled')->count(),
            'confirmed' => (int) (clone $ordersQuery)->where('status', 'confirmed')->count(),
        ];

        $topShops = ShopOrder::with('shop')
            ->whereBetween('order_date', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->when($filters['shop_id'] ?? null, fn($q, $s) => $q->where('shop_id', $s))
            ->select('shop_id', DB::raw('COUNT(*) as total_orders'), DB::raw('SUM(total_amount) as total_sales'))
            ->groupBy('shop_id')
            ->orderByDesc('total_sales')
            ->limit(5)
            ->get();

        // 4. Monthly Products Breakdown
        $topProducts = ShopOrderItem::whereHas('order', function ($q) use ($startDate, $endDate, $filters) {
            $q->whereBetween('order_date', [$startDate, $endDate])
              ->where('status', '!=', 'cancelled')
              ->when($filters['shop_id'] ?? null, fn($sq, $s) => $sq->where('shop_id', $s));
        })->when($filters['product_id'] ?? null, fn($q, $p) => $q->where('product_id', $p))
          ->select('product_id', 'product_name', 'unit', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(line_total) as total_sales'))
          ->groupBy('product_id', 'product_name', 'unit')
          ->orderByDesc('total_sales')
          ->get();

        // 5. Monthly Deliveries
        $deliveriesQuery = Delivery::whereBetween('delivery_date', [$startDate, $endDate])
            ->when($filters['shop_id'] ?? null, fn($q, $s) => $q->where('shop_id', $s));

        $deliveriesCount = (int) $deliveriesQuery->count();
        $deliveriesByStatus = [
            'delivered' => (int) (clone $deliveriesQuery)->where('status', 'delivered')->count(),
            'pending' => (int) (clone $deliveriesQuery)->where('status', 'pending')->count(),
            'failed' => (int) (clone $deliveriesQuery)->where('status', 'failed')->count(),
            'cancelled' => (int) (clone $deliveriesQuery)->where('status', 'cancelled')->count(),
        ];

        return [
            'month' => $month,
            'year' => $year,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'days_in_month' => $daysInMonth,
            'collection' => [
                'total_litres' => $totalCollectedLitres,
                'farmers_count' => $farmersCount,
                'avg_daily' => $avgDailyCollection,
                'total_amount' => $totalCollectionAmount,
                'avg_fat' => $avgFat,
                'avg_snf' => $avgSnf,
                'village_breakdown' => $villageCollections,
            ],
            'center' => [
                'total_received' => $totalReceivedLitres,
                'avg_daily' => $avgDailyReceiving,
                'opening_stock' => $openingStock,
                'stock_in' => $monthlyIn,
                'stock_out' => $monthlyOut,
                'closing_stock' => $closingStock,
            ],
            'orders' => [
                'total_count' => $ordersCount,
                'total_value' => $totalOrderValue,
                'by_status' => $ordersByStatus,
                'top_shops' => $topShops,
            ],
            'products' => [
                'items' => $topProducts,
                'total_units' => (float) $topProducts->sum('total_qty'),
            ],
            'deliveries' => [
                'total_count' => $deliveriesCount,
                'by_status' => $deliveriesByStatus,
            ],
            'financial' => [
                'total_sales' => $totalOrderValue,
                'milk_expense' => $totalCollectionAmount,
                'net_balance' => round($totalOrderValue - $totalCollectionAmount, 2),
            ],
        ];
    }
}
