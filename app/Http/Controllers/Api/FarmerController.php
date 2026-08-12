<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreFarmerRequest;
use App\Http\Requests\Api\UpdateFarmerRequest;
use App\Http\Resources\FarmerResource;
use App\Models\Farmer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FarmerController extends Controller
{
    /**
     * Display a listing of farmers with search, filtering and pagination.
     *
     * GET /api/farmers
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Farmer::class);

        $search = $request->input('search');
        $villageId = $request->input('village_id');
        $status = $request->input('status');
        $perPage = (int) $request->input('per_page', 15);

        $farmers = Farmer::query()
            ->with('village')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('farmer_code', 'like', "%{$search}%")
                      ->orWhere('mobile', 'like', "%{$search}%");
                });
            })
            ->when($villageId, function ($query, $villageId) {
                $query->where('village_id', $villageId);
            })
            ->when($status !== null && $status !== '', function ($query) use ($status) {
                $query->where('status', filter_var($status, FILTER_VALIDATE_BOOLEAN));
            })
            ->latest()
            ->paginate($perPage > 0 ? $perPage : 15)
            ->withQueryString();

        return response()->json([
            'success' => true,
            'message' => 'Farmers retrieved successfully',
            'data' => FarmerResource::collection($farmers),
            'meta' => [
                'current_page' => $farmers->currentPage(),
                'last_page' => $farmers->lastPage(),
                'per_page' => $farmers->perPage(),
                'total' => $farmers->total(),
            ],
            'links' => [
                'first' => $farmers->url(1),
                'last' => $farmers->url($farmers->lastPage()),
                'prev' => $farmers->previousPageUrl(),
                'next' => $farmers->nextPageUrl(),
            ],
        ], 200);
    }

    /**
     * Store a newly created farmer in storage.
     *
     * POST /api/farmers
     */
    public function store(StoreFarmerRequest $request): JsonResponse
    {
        Gate::authorize('create', Farmer::class);

        $validated = $request->validated();
        if (! array_key_exists('status', $validated) || $validated['status'] === null) {
            $validated['status'] = true;
        }

        $farmer = Farmer::create($validated);
        $farmer->load('village');

        return response()->json([
            'success' => true,
            'message' => 'Farmer created successfully',
            'data' => new FarmerResource($farmer),
        ], 201);
    }

    /**
     * Display the specified farmer.
     *
     * GET /api/farmers/{farmer}
     */
    public function show(Farmer $farmer): JsonResponse
    {
        Gate::authorize('view', $farmer);

        $farmer->load('village');

        return response()->json([
            'success' => true,
            'message' => 'Farmer retrieved successfully',
            'data' => new FarmerResource($farmer),
        ], 200);
    }

    /**
     * Update the specified farmer in storage.
     *
     * PUT/PATCH /api/farmers/{farmer}
     */
    public function update(UpdateFarmerRequest $request, Farmer $farmer): JsonResponse
    {
        Gate::authorize('update', $farmer);

        $validated = $request->validated();

        $farmer->update($validated);
        $farmer->load('village');

        return response()->json([
            'success' => true,
            'message' => 'Farmer updated successfully',
            'data' => new FarmerResource($farmer),
        ], 200);
    }

    /**
     * Remove the specified farmer from storage.
     *
     * DELETE /api/farmers/{farmer}
     */
    public function destroy(Farmer $farmer): JsonResponse
    {
        Gate::authorize('delete', $farmer);

        $farmer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Farmer deleted successfully',
            'data' => null,
        ], 200);
    }
}
