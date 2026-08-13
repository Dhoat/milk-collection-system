<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreMilkStockOutRequest;
use App\Http\Resources\MilkStockResource;
use App\Models\MilkStock;
use App\Services\MilkStockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MilkStockController extends Controller
{
    /**
     * Display a listing of milk stock transactions with filters and KPI metrics.
     *
     * GET /api/milk-stocks
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', MilkStock::class);

        $filterDate = $request->input('date');
        $filterType = $request->input('type');
        $search = $request->input('search');

        $query = MilkStock::with(['milkReceiving.village', 'creator'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc');

        if ($filterDate) {
            $query->whereDate('transaction_date', $filterDate);
        }

        if ($filterType && in_array($filterType, ['in', 'out'], true)) {
            $query->where('type', $filterType);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('source_or_reason', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 15);
        $transactions = $query->paginate($perPage > 0 ? $perPage : 15)->withQueryString();

        // Calculate KPI summary metrics
        $openingStock = MilkStock::getOpeningStock($filterDate);
        $todayReceived = MilkStock::getTodayReceived($filterDate);
        $todayStockOut = MilkStock::getTodayStockOut($filterDate);
        $availableStock = MilkStock::getAvailableStock();

        return response()->json([
            'success' => true,
            'message' => 'Milk stock transactions retrieved successfully',
            'summary' => [
                'opening_stock' => $openingStock,
                'today_received' => $todayReceived,
                'today_stock_out' => $todayStockOut,
                'available_stock' => $availableStock,
            ],
            'data' => MilkStockResource::collection($transactions),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ],
            'links' => [
                'first' => $transactions->url(1),
                'last' => $transactions->url($transactions->lastPage()),
                'prev' => $transactions->previousPageUrl(),
                'next' => $transactions->nextPageUrl(),
            ],
        ], 200);
    }

    /**
     * Display the specified milk stock record details.
     *
     * GET /api/milk-stocks/{milkStock}
     */
    public function show(MilkStock $milkStock): JsonResponse
    {
        Gate::authorize('view', $milkStock);

        $milkStock->load(['milkReceiving.village', 'creator']);

        return response()->json([
            'success' => true,
            'message' => 'Milk stock record retrieved successfully',
            'data' => new MilkStockResource($milkStock),
        ], 200);
    }

    /**
     * Store a manual Stock OUT transaction.
     *
     * POST /api/milk-stocks/out
     */
    public function storeOut(StoreMilkStockOutRequest $request, MilkStockService $stockService): JsonResponse
    {
        Gate::authorize('createOut', MilkStock::class);

        $available = MilkStock::getAvailableStock();
        if ((float) $request->quantity > $available) {
            return response()->json([
                'success' => false,
                'message' => 'The given data was invalid.',
                'errors' => [
                    'quantity' => [
                        sprintf(
                            'Stock OUT quantity (%.2f L) cannot exceed current available stock (%.2f L).',
                            (float) $request->quantity,
                            $available
                        ),
                    ],
                ],
            ], 422);
        }

        try {
            $stockOut = $stockService->recordStockOut($request->validated(), auth()->id());
            $stockOut->load(['milkReceiving.village', 'creator']);

            return response()->json([
                'success' => true,
                'message' => 'Stock OUT transaction recorded successfully',
                'data' => new MilkStockResource($stockOut),
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => 'The given data was invalid.',
                'errors' => [
                    'quantity' => [$e->getMessage()],
                ],
            ], 422);
        }
    }
}
