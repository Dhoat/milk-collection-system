<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreShopRequest;
use App\Http\Requests\Api\UpdateShopRequest;
use App\Http\Resources\ShopResource;
use App\Models\Shop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ShopController extends Controller
{
    /**
     * Display a listing of shops with filtering and search.
     *
     * GET /api/shops
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Shop::class);

        $query = Shop::with('village')->filter($request->all())->latest();

        $perPage = (int) $request->input('per_page', 15);
        $shops = $query->paginate($perPage > 0 ? $perPage : 15)->withQueryString();

        return response()->json([
            'success' => true,
            'message' => 'Shops retrieved successfully',
            'data' => ShopResource::collection($shops),
            'meta' => [
                'current_page' => $shops->currentPage(),
                'last_page' => $shops->lastPage(),
                'per_page' => $shops->perPage(),
                'total' => $shops->total(),
            ],
            'links' => [
                'first' => $shops->url(1),
                'last' => $shops->url($shops->lastPage()),
                'prev' => $shops->previousPageUrl(),
                'next' => $shops->nextPageUrl(),
            ],
        ], 200);
    }

    /**
     * Store a newly created shop in storage.
     *
     * POST /api/shops
     */
    public function store(StoreShopRequest $request): JsonResponse
    {
        Gate::authorize('create', Shop::class);

        $shop = Shop::create($request->validated());
        $shop->load('village');

        return response()->json([
            'success' => true,
            'message' => 'Shop created successfully',
            'data' => new ShopResource($shop),
        ], 201);
    }

    /**
     * Display the specified shop details.
     *
     * GET /api/shops/{shop}
     */
    public function show(Shop $shop): JsonResponse
    {
        Gate::authorize('view', $shop);

        $shop->load('village');

        return response()->json([
            'success' => true,
            'message' => 'Shop retrieved successfully',
            'data' => new ShopResource($shop),
        ], 200);
    }

    /**
     * Update the specified shop in storage.
     *
     * PUT/PATCH /api/shops/{shop}
     */
    public function update(UpdateShopRequest $request, Shop $shop): JsonResponse
    {
        Gate::authorize('update', $shop);

        $shop->update($request->validated());
        $shop->load('village');

        return response()->json([
            'success' => true,
            'message' => 'Shop updated successfully',
            'data' => new ShopResource($shop),
        ], 200);
    }

    /**
     * Toggle active/inactive status of the specified shop.
     *
     * PATCH /api/shops/{shop}/toggle-status
     */
    public function toggleStatus(Shop $shop): JsonResponse
    {
        Gate::authorize('update', $shop);

        $shop->update(['status' => !$shop->status]);
        $shop->load('village');

        return response()->json([
            'success' => true,
            'message' => 'Shop status updated successfully',
            'data' => new ShopResource($shop),
        ], 200);
    }

    /**
     * Remove the specified shop from storage.
     *
     * DELETE /api/shops/{shop}
     */
    public function destroy(Shop $shop): JsonResponse
    {
        Gate::authorize('delete', $shop);

        $shop->delete();

        return response()->json([
            'success' => true,
            'message' => 'Shop deleted successfully',
            'data' => null,
        ], 200);
    }
}
