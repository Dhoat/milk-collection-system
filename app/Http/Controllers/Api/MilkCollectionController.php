<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreMilkCollectionRequest;
use App\Http\Requests\Api\UpdateMilkCollectionRequest;
use App\Http\Resources\MilkCollectionResource;
use App\Models\MilkCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MilkCollectionController extends Controller
{
    /**
     * Display a listing of milk collections with search, filtering and pagination.
     *
     * GET /api/milk-collections
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', MilkCollection::class);

        $search = $request->input('search');
        $villageId = $request->input('village_id');
        $farmerId = $request->input('farmer_id');
        $date = $request->input('date');
        $shift = $request->input('shift');
        $perPage = (int) $request->input('per_page', 15);

        $collections = MilkCollection::query()
            ->with(['farmer.village'])
            ->when($search, function ($query, $search) {
                $query->whereHas('farmer', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('farmer_code', 'like', "%{$search}%");
                });
            })
            ->when($villageId, function ($query, $villageId) {
                $query->whereHas('farmer', function ($q) use ($villageId) {
                    $q->where('village_id', $villageId);
                });
            })
            ->when($farmerId, function ($query, $farmerId) {
                $query->where('farmer_id', $farmerId);
            })
            ->when($date, function ($query, $date) {
                $query->whereDate('collection_date', $date);
            })
            ->when($shift, function ($query, $shift) {
                $query->where('shift', $shift);
            })
            ->latest('collection_date')
            ->latest('created_at')
            ->paginate($perPage > 0 ? $perPage : 15)
            ->withQueryString();

        return response()->json([
            'success' => true,
            'message' => 'Milk collections retrieved successfully',
            'data' => MilkCollectionResource::collection($collections),
            'meta' => [
                'current_page' => $collections->currentPage(),
                'last_page' => $collections->lastPage(),
                'per_page' => $collections->perPage(),
                'total' => $collections->total(),
            ],
            'links' => [
                'first' => $collections->url(1),
                'last' => $collections->url($collections->lastPage()),
                'prev' => $collections->previousPageUrl(),
                'next' => $collections->nextPageUrl(),
            ],
        ], 200);
    }

    /**
     * Store a newly created milk collection entry in storage.
     *
     * POST /api/milk-collections
     */
    public function store(StoreMilkCollectionRequest $request): JsonResponse
    {
        Gate::authorize('create', MilkCollection::class);

        $validated = $request->validated();

        // Single source of truth calculation: amount = milk_quantity * rate
        $validated['amount'] = $validated['milk_quantity'] * $validated['rate'];

        $milkCollection = MilkCollection::create($validated);
        $milkCollection->load(['farmer.village']);

        return response()->json([
            'success' => true,
            'message' => 'Milk collection created successfully',
            'data' => new MilkCollectionResource($milkCollection),
        ], 201);
    }

    /**
     * Display the specified milk collection entry details.
     *
     * GET /api/milk-collections/{milkCollection}
     */
    public function show(MilkCollection $milkCollection): JsonResponse
    {
        Gate::authorize('view', $milkCollection);

        $milkCollection->load(['farmer.village']);

        return response()->json([
            'success' => true,
            'message' => 'Milk collection retrieved successfully',
            'data' => new MilkCollectionResource($milkCollection),
        ], 200);
    }

    /**
     * Update the specified milk collection entry in storage.
     *
     * PUT/PATCH /api/milk-collections/{milkCollection}
     */
    public function update(UpdateMilkCollectionRequest $request, MilkCollection $milkCollection): JsonResponse
    {
        Gate::authorize('update', $milkCollection);

        $validated = $request->validated();

        // Single source of truth calculation: amount = milk_quantity * rate
        $validated['amount'] = $validated['milk_quantity'] * $validated['rate'];

        $milkCollection->update($validated);
        $milkCollection->load(['farmer.village']);

        return response()->json([
            'success' => true,
            'message' => 'Milk collection updated successfully',
            'data' => new MilkCollectionResource($milkCollection),
        ], 200);
    }

    /**
     * Remove the specified milk collection entry from storage.
     *
     * DELETE /api/milk-collections/{milkCollection}
     */
    public function destroy(MilkCollection $milkCollection): JsonResponse
    {
        Gate::authorize('delete', $milkCollection);

        $milkCollection->delete();

        return response()->json([
            'success' => true,
            'message' => 'Milk collection deleted successfully',
            'data' => null,
        ], 200);
    }
}
