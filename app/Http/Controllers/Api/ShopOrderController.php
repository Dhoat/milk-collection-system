<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreShopOrderRequest;
use App\Http\Requests\Api\UpdateShopOrderStatusRequest;
use App\Http\Resources\ShopOrderResource;
use App\Models\ShopOrder;
use App\Services\ShopOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use InvalidArgumentException;

class ShopOrderController extends Controller
{
    /**
     * Display a listing of shop orders with filtering and search.
     *
     * GET /api/shop-orders
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', ShopOrder::class);

        $query = ShopOrder::with(['shop', 'creator', 'items.product'])
            ->filter($request->all())
            ->latest();

        $perPage = (int) $request->input('per_page', 15);
        $orders = $query->paginate($perPage > 0 ? $perPage : 15)->withQueryString();

        return response()->json([
            'success' => true,
            'message' => 'Shop orders retrieved successfully',
            'data' => ShopOrderResource::collection($orders),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
            'links' => [
                'first' => $orders->url(1),
                'last' => $orders->url($orders->lastPage()),
                'prev' => $orders->previousPageUrl(),
                'next' => $orders->nextPageUrl(),
            ],
        ], 200);
    }

    /**
     * Store a newly created shop order in storage.
     *
     * POST /api/shop-orders
     */
    public function store(StoreShopOrderRequest $request, ShopOrderService $orderService): JsonResponse
    {
        Gate::authorize('create', ShopOrder::class);

        try {
            $order = $orderService->createOrder(
                $request->only(['shop_id', 'order_date', 'status', 'discount', 'notes']),
                $request->input('items'),
                auth()->id()
            );

            $order->load(['shop', 'creator', 'items.product']);

            return response()->json([
                'success' => true,
                'message' => 'Shop order created successfully',
                'data' => new ShopOrderResource($order),
            ], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => [
                    'items' => [$e->getMessage()],
                ],
            ], 422);
        }
    }

    /**
     * Display the specified shop order details.
     *
     * GET /api/shop-orders/{shopOrder}
     */
    public function show(ShopOrder $shopOrder): JsonResponse
    {
        Gate::authorize('view', $shopOrder);

        $shopOrder->load(['shop', 'creator', 'items.product']);

        return response()->json([
            'success' => true,
            'message' => 'Shop order retrieved successfully',
            'data' => new ShopOrderResource($shopOrder),
        ], 200);
    }

    /**
     * Update the status of the specified shop order.
     *
     * PATCH /api/shop-orders/{shopOrder}/status
     * PUT/PATCH /api/shop-orders/{shopOrder}
     */
    public function updateStatus(UpdateShopOrderStatusRequest $request, ShopOrder $shopOrder, ShopOrderService $orderService): JsonResponse
    {
        Gate::authorize('updateStatus', $shopOrder);

        try {
            $orderService->updateOrderStatus($shopOrder, $request->input('status'));

            $shopOrder->load(['shop', 'creator', 'items.product']);

            return response()->json([
                'success' => true,
                'message' => 'Shop order status updated successfully',
                'data' => new ShopOrderResource($shopOrder),
            ], 200);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => [
                    'status' => [$e->getMessage()],
                ],
            ], 422);
        }
    }

    /**
     * Remove the specified shop order from storage.
     *
     * DELETE /api/shop-orders/{shopOrder}
     */
    public function destroy(ShopOrder $shopOrder, ShopOrderService $orderService): JsonResponse
    {
        Gate::authorize('delete', $shopOrder);

        // If order stock was deducted, reverse it before deletion
        if ($shopOrder->stock_deducted) {
            $orderService->updateOrderStatus($shopOrder, 'cancelled');
        }

        $shopOrder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Shop order deleted successfully',
            'data' => null,
        ], 200);
    }
}
