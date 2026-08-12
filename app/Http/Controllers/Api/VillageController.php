<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreVillageRequest;
use App\Http\Requests\Api\UpdateVillageRequest;
use App\Http\Resources\VillageResource;
use App\Models\Village;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class VillageController extends Controller
{
    /**
     * Display a listing of villages with search and pagination support.
     *
     * GET /api/villages
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Village::class);

        $search = $request->input('search');
        $status = $request->input('status');
        $perPage = (int) $request->input('per_page', 15);

        $villages = Village::query()
            ->withCount('farmers')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when($status !== null && $status !== '', function ($query) use ($status) {
                $query->where('status', filter_var($status, FILTER_VALIDATE_BOOLEAN));
            })
            ->latest()
            ->paginate($perPage > 0 ? $perPage : 15)
            ->withQueryString();

        return response()->json([
            'success' => true,
            'message' => 'Villages retrieved successfully',
            'data' => VillageResource::collection($villages),
            'meta' => [
                'current_page' => $villages->currentPage(),
                'last_page' => $villages->lastPage(),
                'per_page' => $villages->perPage(),
                'total' => $villages->total(),
            ],
            'links' => [
                'first' => $villages->url(1),
                'last' => $villages->url($villages->lastPage()),
                'prev' => $villages->previousPageUrl(),
                'next' => $villages->nextPageUrl(),
            ],
        ], 200);
    }

    /**
     * Store a newly created village in storage.
     *
     * POST /api/villages
     */
    public function store(StoreVillageRequest $request): JsonResponse
    {
        Gate::authorize('create', Village::class);

        $validated = $request->validated();
        if (! array_key_exists('status', $validated) || $validated['status'] === null) {
            $validated['status'] = true;
        }

        $village = Village::create($validated);
        $village->loadCount('farmers');

        return response()->json([
            'success' => true,
            'message' => 'Village created successfully',
            'data' => new VillageResource($village),
        ], 201);
    }

    /**
     * Display the specified village details.
     *
     * GET /api/villages/{village}
     */
    public function show(Village $village): JsonResponse
    {
        Gate::authorize('view', $village);

        $village->loadCount('farmers');

        return response()->json([
            'success' => true,
            'message' => 'Village retrieved successfully',
            'data' => new VillageResource($village),
        ], 200);
    }

    /**
     * Update the specified village in storage.
     *
     * PUT/PATCH /api/villages/{village}
     */
    public function update(UpdateVillageRequest $request, Village $village): JsonResponse
    {
        Gate::authorize('update', $village);

        $validated = $request->validated();

        $village->update($validated);
        $village->loadCount('farmers');

        return response()->json([
            'success' => true,
            'message' => 'Village updated successfully',
            'data' => new VillageResource($village),
        ], 200);
    }

    /**
     * Remove the specified village from storage.
     *
     * DELETE /api/villages/{village}
     */
    public function destroy(Village $village): JsonResponse
    {
        Gate::authorize('delete', $village);

        $village->delete();

        return response()->json([
            'success' => true,
            'message' => 'Village deleted successfully',
            'data' => null,
        ], 200);
    }
}
