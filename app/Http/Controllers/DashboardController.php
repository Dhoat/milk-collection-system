<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the milk center admin dashboard.
     */
    public function index(Request $request, DashboardService $dashboardService): View
    {
        $date = $request->query('date');
        $dashboardData = $dashboardService->getDashboardData($date);

        return view('dashboard', $dashboardData);
    }
}

