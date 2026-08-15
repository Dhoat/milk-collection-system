<?php

namespace App\Http\Controllers;

use App\Models\MilkCollection;
use App\Models\MilkReceiving;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopOrder;
use App\Models\Village;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display the Daily Reports Dashboard.
     */
    public function daily(Request $request, ReportService $reportService): View
    {
        Gate::authorize('view-reports');

        $date = $request->query('date');

        if (! $date) {
            // Default to today, or latest date with records if today has no collections
            $today = date('Y-m-d');
            $hasTodayData = MilkCollection::whereDate('collection_date', $today)->exists() ||
                            MilkReceiving::whereDate('receiving_date', $today)->exists() ||
                            ShopOrder::whereDate('order_date', $today)->exists();

            if (! $hasTodayData) {
                $date = MilkCollection::max('collection_date')
                    ?? MilkReceiving::max('receiving_date')
                    ?? ShopOrder::max('order_date')
                    ?? $today;
            } else {
                $date = $today;
            }
        }

        $filters = [
            'village_id' => $request->query('village_id'),
            'shop_id' => $request->query('shop_id'),
            'product_id' => $request->query('product_id'),
        ];

        $reportData = $reportService->getDailyReport($date, $filters);

        $villages = Village::orderBy('name')->get();
        $shops = Shop::active()->orderBy('name')->get();
        $products = Product::where('status', true)->orderBy('name')->get();

        return view('reports.daily', compact('date', 'reportData', 'villages', 'shops', 'products'));
    }

    /**
     * Display the Monthly Reports Dashboard.
     */
    public function monthly(Request $request, ReportService $reportService): View
    {
        Gate::authorize('view-reports');

        $month = $request->query('month');
        $year = $request->query('year');

        if (! $month || ! $year) {
            $currentMonth = (int) date('n');
            $currentYear = (int) date('Y');

            $hasMonthData = MilkCollection::whereYear('collection_date', $currentYear)->whereMonth('collection_date', $currentMonth)->exists() ||
                            MilkReceiving::whereYear('receiving_date', $currentYear)->whereMonth('receiving_date', $currentMonth)->exists() ||
                            ShopOrder::whereYear('order_date', $currentYear)->whereMonth('order_date', $currentMonth)->exists();

            if (! $hasMonthData) {
                $latestDate = MilkCollection::max('collection_date')
                    ?? MilkReceiving::max('receiving_date')
                    ?? ShopOrder::max('order_date');

                if ($latestDate) {
                    $timestamp = strtotime($latestDate);
                    $month = $month ?: (int) date('n', $timestamp);
                    $year = $year ?: (int) date('Y', $timestamp);
                }
            }

            $month = (int) ($month ?: date('n'));
            $year = (int) ($year ?: date('Y'));
        } else {
            $month = (int) $month;
            $year = (int) $year;
        }

        $filters = [
            'village_id' => $request->query('village_id'),
            'shop_id' => $request->query('shop_id'),
            'product_id' => $request->query('product_id'),
        ];

        $reportData = $reportService->getMonthlyReport($month, $year, $filters);

        $villages = Village::orderBy('name')->get();
        $shops = Shop::active()->orderBy('name')->get();
        $products = Product::where('status', true)->orderBy('name')->get();

        return view('reports.monthly', compact('month', 'year', 'reportData', 'villages', 'shops', 'products'));
    }
}
