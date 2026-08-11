<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Shop;
use App\Models\ShopOrder;
use App\Models\User;
use App\Services\DeliveryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use InvalidArgumentException;

class DeliveryController extends Controller
{
    /**
     * Display a listing of deliveries with filtering and search.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Delivery::class);

        $deliveries = Delivery::with(['shopOrder', 'shop', 'assignedStaff', 'creator'])
            ->filter($request->all())
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $shops = Shop::active()->orderBy('name')->get();
        $staffUsers = User::whereIn('role', ['super_admin', 'manager', 'center_staff'])->orderBy('name')->get();

        return view('deliveries.index', compact('deliveries', 'shops', 'staffUsers'));
    }

    /**
     * Show the form for creating a new delivery dispatch.
     */
    public function create(Request $request): View
    {
        Gate::authorize('create', Delivery::class);

        $selectedOrderId = $request->query('order_id');

        $orders = ShopOrder::with(['shop', 'items'])
            ->whereIn('status', ['confirmed', 'preparing', 'dispatched', 'pending'])
            ->latest()
            ->get();

        $staffUsers = User::whereIn('role', ['super_admin', 'manager', 'center_staff'])->orderBy('name')->get();

        return view('deliveries.create', compact('orders', 'staffUsers', 'selectedOrderId'));
    }

    /**
     * Store a newly created delivery record in storage.
     */
    public function store(Request $request, DeliveryService $deliveryService): RedirectResponse
    {
        Gate::authorize('create', Delivery::class);

        $validated = $request->validate([
            'shop_order_id' => 'required|exists:shop_orders,id',
            'delivery_date' => 'required|date',
            'status' => 'required|in:pending,assigned,out_for_delivery,delivered,failed,cancelled',
            'assigned_to' => 'nullable|exists:users,id',
            'delivery_address' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $order = ShopOrder::findOrFail($request->input('shop_order_id'));

        try {
            $delivery = $deliveryService->createDelivery($order, $validated, auth()->id());

            return redirect()->route('deliveries.index')
                ->with('success', __('Delivery :number created for order :order successfully.', [
                    'number' => $delivery->delivery_number,
                    'order' => $order->order_number,
                ]));
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['shop_order_id' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified delivery details.
     */
    public function show(Delivery $delivery): View
    {
        Gate::authorize('view', $delivery);

        $delivery->load(['shopOrder.items.product', 'shop', 'assignedStaff', 'creator']);
        $staffUsers = User::whereIn('role', ['super_admin', 'manager', 'center_staff'])->orderBy('name')->get();

        return view('deliveries.show', compact('delivery', 'staffUsers'));
    }

    /**
     * Update delivery status and assignment.
     */
    public function updateStatus(Request $request, Delivery $delivery, DeliveryService $deliveryService): RedirectResponse
    {
        Gate::authorize('updateStatus', $delivery);

        $validated = $request->validate([
            'status' => 'required|in:pending,assigned,out_for_delivery,delivered,failed,cancelled',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        try {
            $assignedTo = $request->has('assigned_to') ? ($request->input('assigned_to') ? (int) $request->input('assigned_to') : null) : null;
            $deliveryService->updateDeliveryStatus($delivery, $validated['status'], $assignedTo);

            return back()->with('success', __('Delivery status updated to :status successfully.', ['status' => ucfirst($delivery->status)]));
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified delivery record from storage.
     */
    public function destroy(Delivery $delivery): RedirectResponse
    {
        Gate::authorize('delete', $delivery);

        $delivery->delete();

        return redirect()->route('deliveries.index')
            ->with('success', __('Delivery dispatch record deleted successfully.'));
    }
}
