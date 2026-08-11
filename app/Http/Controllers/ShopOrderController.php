<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopOrder;
use App\Services\ShopOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use InvalidArgumentException;

class ShopOrderController extends Controller
{
    /**
     * Display a listing of shop orders with filtering and search.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', ShopOrder::class);

        $orders = ShopOrder::with(['shop', 'creator', 'items.product'])
            ->filter($request->all())
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $shops = Shop::active()->orderBy('name')->get();

        return view('shop_orders.index', compact('orders', 'shops'));
    }

    /**
     * Show the form for creating a new shop order.
     */
    public function create(): View
    {
        Gate::authorize('create', ShopOrder::class);

        $shops = Shop::active()->orderBy('name')->get();
        $products = Product::where('status', true)->orderBy('name')->get();

        return view('shop_orders.create', compact('shops', 'products'));
    }

    /**
     * Store a newly created shop order in storage.
     */
    public function store(Request $request, ShopOrderService $orderService): RedirectResponse
    {
        Gate::authorize('create', ShopOrder::class);

        $validated = $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'order_date' => 'required|date',
            'status' => 'required|in:pending,confirmed,preparing,dispatched,delivered,cancelled',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.unit_price' => 'nullable|numeric|min:0',
        ]);

        try {
            $order = $orderService->createOrder(
                $request->only(['shop_id', 'order_date', 'status', 'discount', 'notes']),
                $request->input('items'),
                auth()->id()
            );

            return redirect()->route('shop-orders.index')
                ->with('success', __('Order :number placed successfully.', ['number' => $order->order_number]));
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['items' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified shop order details.
     */
    public function show(ShopOrder $shopOrder): View
    {
        Gate::authorize('view', $shopOrder);

        $shopOrder->load(['shop', 'creator', 'items.product']);

        return view('shop_orders.show', ['order' => $shopOrder]);
    }

    /**
     * Update order status and trigger stock deduction / reversal if required.
     */
    public function updateStatus(Request $request, ShopOrder $shopOrder, ShopOrderService $orderService): RedirectResponse
    {
        Gate::authorize('updateStatus', $shopOrder);

        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,dispatched,delivered,cancelled',
        ]);

        try {
            $orderService->updateOrderStatus($shopOrder, $request->input('status'));

            return back()->with('success', __('Order status updated to :status successfully.', ['status' => ucfirst($shopOrder->status)]));
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified shop order from storage.
     */
    public function destroy(ShopOrder $shopOrder, ShopOrderService $orderService): RedirectResponse
    {
        Gate::authorize('delete', $shopOrder);

        // If order stock was deducted, reverse it before deletion
        if ($shopOrder->stock_deducted) {
            $orderService->updateOrderStatus($shopOrder, 'cancelled');
        }

        $shopOrder->delete();

        return redirect()->route('shop-orders.index')
            ->with('success', __('Shop order deleted successfully.'));
    }
}
