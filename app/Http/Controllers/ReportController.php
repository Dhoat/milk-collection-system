<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shop;
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

        $date = $request->query('date', date('Y-m-d'));

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

        $month = (int) $request->query('month', date('n'));
        $year = (int) $request->query('year', date('Y'));

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
