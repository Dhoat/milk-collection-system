<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Village;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ShopController extends Controller
{
    /**
     * Display a listing of the shops with filtering and search.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Shop::class);

        $query = Shop::with('village')->filter($request->all())->latest();

        $shops = $query->paginate(10)->withQueryString();
        $villages = Village::orderBy('name')->get();

        return view('shops.index', compact('shops', 'villages'));
    }

    /**
     * Show the form for creating a new shop.
     */
    public function create(): View
    {
        Gate::authorize('create', Shop::class);

        $villages = Village::orderBy('name')->get();
        return view('shops.create', compact('villages'));
    }

    /**
     * Store a newly created shop in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Shop::class);

        $validated = $request->validate([
            'shop_code' => 'required|string|max:50|unique:shops,shop_code',
            'name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'village_id' => 'nullable|exists:villages,id',
            'area' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'status' => 'required|boolean',
            'credit_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        Shop::create($validated);

        return redirect()->route('shops.index')
            ->with('success', __('Shop registered successfully.'));
    }

    /**
     * Display the specified shop details.
     */
    public function show(Shop $shop): View
    {
        Gate::authorize('view', $shop);

        $shop->load('village');

        return view('shops.show', compact('shop'));
    }

    /**
     * Show the form for editing the specified shop.
     */
    public function edit(Shop $shop): View
    {
        Gate::authorize('update', $shop);

        $villages = Village::orderBy('name')->get();
        return view('shops.edit', compact('shop', 'villages'));
    }

    /**
     * Update the specified shop in storage.
     */
    public function update(Request $request, Shop $shop): RedirectResponse
    {
        Gate::authorize('update', $shop);

        $validated = $request->validate([
            'shop_code' => ['required', 'string', 'max:50', Rule::unique('shops', 'shop_code')->ignore($shop->id)],
            'name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'village_id' => 'nullable|exists:villages,id',
            'area' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'status' => 'required|boolean',
            'credit_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $shop->update($validated);

        return redirect()->route('shops.index')
            ->with('success', __('Shop updated successfully.'));
    }

    /**
     * Toggle active/inactive status of the shop.
     */
    public function toggleStatus(Shop $shop): RedirectResponse
    {
        Gate::authorize('update', $shop);

        $shop->update(['status' => !$shop->status]);

        $statusText = $shop->status ? __('activated') : __('deactivated');
        return redirect()->back()->with('success', __("Shop {$statusText} successfully."));
    }

    /**
     * Remove the specified shop from storage.
     */
    public function destroy(Shop $shop): RedirectResponse
    {
        Gate::authorize('delete', $shop);

        $shop->delete();

        return redirect()->route('shops.index')
            ->with('success', __('Shop deleted successfully.'));
    }
}
