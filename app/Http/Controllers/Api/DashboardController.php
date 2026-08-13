<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the milk center dashboard summary for the API.
     *
     * GET /api/dashboard
     */
    public function index(Request $request, DashboardService $dashboardService): JsonResponse
    {
        $request->validate([
            'date' => 'nullable|date',
        ]);

        $date = $request->query('date');
        $dashboardData = $dashboardService->getDashboardData($date);

        // Transform Carbon instance to string representation for API format
        $dashboardData['today'] = $dashboardData['today']->toDateString();

        return response()->json([
            'success' => true,
            'message' => 'Dashboard data retrieved successfully',
            'data' => $dashboardData,
        ], 200);
    }
}
