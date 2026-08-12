<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreMilkReceivingRequest;
use App\Http\Requests\Api\UpdateMilkReceivingRequest;
use App\Http\Resources\MilkReceivingResource;
use App\Models\MilkCollection;
use App\Models\MilkReceiving;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MilkReceivingController extends Controller
{
    /**
     * Display a listing of milk receiving records.
     *
     * GET /api/milk-receivings
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', MilkReceiving::class);

        $query = MilkReceiving::with(['village', 'verifier'])->latest('receiving_date');

        if ($request->filled('date')) {
            $query->whereDate('receiving_date', $request->date);
        }

        if ($request->filled('village_id')) {
            $query->where('village_id', $request->village_id);
        }

        if ($request->filled('shift')) {
            $query->where('shift', $request->shift);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = (int) $request->input('per_page', 15);
        $receivings = $query->paginate($perPage > 0 ? $perPage : 15)->withQueryString();

        return response()->json([
            'success' => true,
            'message' => 'Milk receiving records retrieved successfully',
            'data' => MilkReceivingResource::collection($receivings),
            'meta' => [
                'current_page' => $receivings->currentPage(),
                'last_page' => $receivings->lastPage(),
                'per_page' => $receivings->perPage(),
                'total' => $receivings->total(),
            ],
            'links' => [
                'first' => $receivings->url(1),
                'last' => $receivings->url($receivings->lastPage()),
                'prev' => $receivings->previousPageUrl(),
                'next' => $receivings->nextPageUrl(),
            ],
        ], 200);
    }

    /**
     * Store a newly created milk receiving record in storage.
     *
     * POST /api/milk-receivings
     */
    public function store(StoreMilkReceivingRequest $request): JsonResponse
    {
        Gate::authorize('create', MilkReceiving::class);

        $metrics = $this->calculateExpectedMetrics(
            (int) $request->village_id,
            (string) $request->receiving_date,
            (string) $request->shift
        );

        $qtyDiff = abs((float) $request->received_quantity - $metrics['expected_quantity']);
        $status = $qtyDiff > 0.1 ? 'discrepancy' : 'received';

        $receiving = MilkReceiving::create([
            'village_id' => $request->village_id,
            'receiving_date' => $request->receiving_date,
            'shift' => $request->shift,
            'expected_quantity' => $metrics['expected_quantity'],
            'received_quantity' => $request->received_quantity,
            'expected_fat' => $metrics['expected_fat'],
            'received_fat' => $request->received_fat,
            'expected_snf' => $metrics['expected_snf'],
            'received_snf' => $request->received_snf,
            'status' => $status,
            'verified_by' => auth()->id(),
            'notes' => $request->notes,
        ]);

        $receiving->load(['village', 'verifier']);

        return response()->json([
            'success' => true,
            'message' => 'Milk receiving record created successfully',
            'data' => new MilkReceivingResource($receiving),
        ], 201);
    }

    /**
     * Display the specified milk receiving record.
     *
     * GET /api/milk-receivings/{milkReceiving}
     */
    public function show(MilkReceiving $milkReceiving): JsonResponse
    {
        Gate::authorize('view', $milkReceiving);

        $milkReceiving->load(['village', 'verifier']);

        return response()->json([
            'success' => true,
            'message' => 'Milk receiving record retrieved successfully',
            'data' => new MilkReceivingResource($milkReceiving),
        ], 200);
    }

    /**
     * Update the specified milk receiving record in storage.
     *
     * PUT/PATCH /api/milk-receivings/{milkReceiving}
     */
    public function update(UpdateMilkReceivingRequest $request, MilkReceiving $milkReceiving): JsonResponse
    {
        Gate::authorize('update', $milkReceiving);

        $metrics = $this->calculateExpectedMetrics(
            (int) $request->village_id,
            (string) $request->receiving_date,
            (string) $request->shift
        );

        $qtyDiff = abs((float) $request->received_quantity - $metrics['expected_quantity']);
        $status = $qtyDiff > 0.1 ? 'discrepancy' : 'received';

        $milkReceiving->update([
            'village_id' => $request->village_id,
            'receiving_date' => $request->receiving_date,
            'shift' => $request->shift,
            'expected_quantity' => $metrics['expected_quantity'],
            'received_quantity' => $request->received_quantity,
            'expected_fat' => $metrics['expected_fat'],
            'received_fat' => $request->received_fat,
            'expected_snf' => $metrics['expected_snf'],
            'received_snf' => $request->received_snf,
            'status' => $status,
            'verified_by' => auth()->id(),
            'notes' => $request->notes,
        ]);

        $milkReceiving->load(['village', 'verifier']);

        return response()->json([
            'success' => true,
            'message' => 'Milk receiving record updated successfully',
            'data' => new MilkReceivingResource($milkReceiving),
        ], 200);
    }

    /**
     * Remove the specified milk receiving record from storage.
     *
     * DELETE /api/milk-receivings/{milkReceiving}
     */
    public function destroy(MilkReceiving $milkReceiving): JsonResponse
    {
        Gate::authorize('delete', $milkReceiving);

        $milkReceiving->delete();

        return response()->json([
            'success' => true,
            'message' => 'Milk receiving record deleted successfully',
            'data' => null,
        ], 200);
    }

    /**
     * Get expected collection summary metrics for a village, date, and shift.
     *
     * GET /api/village-collection-summary
     */
    public function getCollectionSummary(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', MilkReceiving::class);

        $request->validate([
            'village_id' => 'required|exists:villages,id',
            'date' => 'required|date',
            'shift' => 'required|in:morning,evening',
        ]);

        $metrics = $this->calculateExpectedMetrics(
            (int) $request->village_id,
            (string) $request->date,
            (string) $request->shift
        );

        $collections = MilkCollection::whereHas('farmer', function ($query) use ($request) {
            $query->where('village_id', $request->village_id);
        })
        ->whereDate('collection_date', $request->date)
        ->where('shift', $request->shift)
        ->get();

        return response()->json([
            'success' => true,
            'message' => 'Collection summary retrieved successfully',
            'data' => [
                'expected_quantity' => round($metrics['expected_quantity'], 2),
                'expected_fat' => $metrics['expected_fat'] !== null ? round($metrics['expected_fat'], 2) : null,
                'expected_snf' => $metrics['expected_snf'] !== null ? round($metrics['expected_snf'], 2) : null,
                'farmer_count' => $collections->count(),
            ],
        ], 200);
    }

    /**
     * Helper: Calculate expected metrics using weighted averages from farmer collections.
     */
    private function calculateExpectedMetrics(int $villageId, string $date, string $shift): array
    {
        $collections = MilkCollection::whereHas('farmer', function ($query) use ($villageId) {
            $query->where('village_id', $villageId);
        })
        ->whereDate('collection_date', $date)
        ->where('shift', $shift)
        ->get();

        $expectedQuantity = $collections->sum('milk_quantity');
        $expectedFat = null;
        $expectedSnf = null;

        if ($expectedQuantity > 0) {
            $totalFatVolume = $collections->sum(fn ($c) => $c->milk_quantity * ($c->fat ?? 0));
            $totalSnfVolume = $collections->sum(fn ($c) => $c->milk_quantity * ($c->snf ?? 0));
            $expectedFat = $totalFatVolume / $expectedQuantity;
            $expectedSnf = $totalSnfVolume / $expectedQuantity;
        }

        return [
            'expected_quantity' => $expectedQuantity,
            'expected_fat' => $expectedFat,
            'expected_snf' => $expectedSnf,
        ];
    }
}
