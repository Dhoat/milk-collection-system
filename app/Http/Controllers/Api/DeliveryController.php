<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreDeliveryRequest;
use App\Http\Requests\Api\UpdateDeliveryStatusRequest;
use App\Http\Resources\DeliveryResource;
use App\Models\Delivery;
use App\Models\ShopOrder;
use App\Services\DeliveryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use InvalidArgumentException;

class DeliveryController extends Controller
{
    /**
     * Display a listing of deliveries with filtering and search.
     *
     * GET /api/deliveries
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Delivery::class);

        $query = Delivery::with(['shopOrder', 'shop', 'assignedStaff', 'creator'])
            ->filter($request->all())
            ->latest();

        $perPage = (int) $request->input('per_page', 15);
        $deliveries = $query->paginate($perPage > 0 ? $perPage : 15)->withQueryString();

        return response()->json([
            'success' => true,
            'message' => 'Deliveries retrieved successfully',
            'data' => DeliveryResource::collection($deliveries),
            'meta' => [
                'current_page' => $deliveries->currentPage(),
                'last_page' => $deliveries->lastPage(),
                'per_page' => $deliveries->perPage(),
                'total' => $deliveries->total(),
            ],
            'links' => [
                'first' => $deliveries->url(1),
                'last' => $deliveries->url($deliveries->lastPage()),
                'prev' => $deliveries->previousPageUrl(),
                'next' => $deliveries->nextPageUrl(),
            ],
        ], 200);
    }

    /**
     * Store a newly created delivery record in storage.
     *
     * POST /api/deliveries
     */
    public function store(StoreDeliveryRequest $request, DeliveryService $deliveryService): JsonResponse
    {
        Gate::authorize('create', Delivery::class);

        $order = ShopOrder::findOrFail($request->input('shop_order_id'));

        try {
            $delivery = $deliveryService->createDelivery($order, $request->validated(), auth()->id());
            $delivery->load(['shopOrder', 'shop', 'assignedStaff', 'creator']);

            return response()->json([
                'success' => true,
                'message' => 'Delivery created successfully',
                'data' => new DeliveryResource($delivery),
            ], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => [
                    'shop_order_id' => [$e->getMessage()],
                ],
            ], 422);
        }
    }

    /**
     * Display the specified delivery details.
     *
     * GET /api/deliveries/{delivery}
     */
    public function show(Delivery $delivery): JsonResponse
    {
        Gate::authorize('view', $delivery);

        $delivery->load(['shopOrder.items.product', 'shop', 'assignedStaff', 'creator']);

        return response()->json([
            'success' => true,
            'message' => 'Delivery retrieved successfully',
            'data' => new DeliveryResource($delivery),
        ], 200);
    }

    /**
     * Update delivery status and staff assignment.
     *
     * PATCH /api/deliveries/{delivery}/status
     * PUT/PATCH /api/deliveries/{delivery}
     */
    public function updateStatus(UpdateDeliveryStatusRequest $request, Delivery $delivery, DeliveryService $deliveryService): JsonResponse
    {
        Gate::authorize('updateStatus', $delivery);

        try {
            $assignedTo = $request->has('assigned_to')
                ? ($request->input('assigned_to') ? (int) $request->input('assigned_to') : null)
                : null;

            $deliveryService->updateDeliveryStatus($delivery, $request->input('status'), $assignedTo);
            $delivery->load(['shopOrder', 'shop', 'assignedStaff', 'creator']);

            return response()->json([
                'success' => true,
                'message' => 'Delivery updated successfully',
                'data' => new DeliveryResource($delivery),
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
     * Remove the specified delivery record from storage.
     *
     * DELETE /api/deliveries/{delivery}
     */
    public function destroy(Delivery $delivery): JsonResponse
    {
        Gate::authorize('delete', $delivery);

        $delivery->delete();

        return response()->json([
            'success' => true,
            'message' => 'Delivery deleted successfully',
            'data' => null,
        ], 200);
    }
}
