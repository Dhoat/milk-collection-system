<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DailyReportRequest;
use App\Http\Requests\Api\MonthlyReportRequest;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    /**
     * Display the Daily Reports API data.
     *
     * GET /api/reports/daily
     */
    public function daily(DailyReportRequest $request, ReportService $reportService): JsonResponse
    {
        Gate::authorize('view-reports');

        $date = $request->query('date', date('Y-m-d'));

        $filters = array_filter([
            'village_id' => $request->query('village_id'),
            'shop_id' => $request->query('shop_id'),
            'product_id' => $request->query('product_id'),
        ], fn($value) => !is_null($value) && $value !== '');

        $reportData = $reportService->getDailyReport($date, $filters);

        return response()->json([
            'success' => true,
            'message' => 'Daily report retrieved successfully',
            'data' => $reportData,
        ], 200);
    }

    /**
     * Display the Monthly Reports API data.
     *
     * GET /api/reports/monthly
     */
    public function monthly(MonthlyReportRequest $request, ReportService $reportService): JsonResponse
    {
        Gate::authorize('view-reports');

        $month = (int) $request->query('month', date('n'));
        $year = (int) $request->query('year', date('Y'));

        $filters = array_filter([
            'village_id' => $request->query('village_id'),
            'shop_id' => $request->query('shop_id'),
            'product_id' => $request->query('product_id'),
        ], fn($value) => !is_null($value) && $value !== '');

        $reportData = $reportService->getMonthlyReport($month, $year, $filters);

        return response()->json([
            'success' => true,
            'message' => 'Monthly report retrieved successfully',
            'data' => $reportData,
        ], 200);
    }
}
