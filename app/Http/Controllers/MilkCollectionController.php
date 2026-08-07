<?php

namespace App\Http\Controllers;

use App\Models\Farmer;
use App\Models\MilkCollection;
use App\Models\Village;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MilkCollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', MilkCollection::class);

        $search = $request->input('search');
        $villageId = $request->input('village_id');
        $farmerId = $request->input('farmer_id');
        $date = $request->input('date');
        $shift = $request->input('shift');

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
            ->paginate(10)
            ->withQueryString();

        $villages = Village::orderBy('name')->get();
        $farmers = Farmer::orderBy('name')->get();

        return view('milk_collections.index', compact(
            'collections', 'villages', 'farmers',
            'search', 'villageId', 'farmerId', 'date', 'shift'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', MilkCollection::class);

        // Only active villages and active farmers can register new collections
        $villages = Village::where('status', true)->orderBy('name')->get();
        $farmers = Farmer::where('status', true)->with('village')->orderBy('name')->get();

        return view('milk_collections.create', compact('villages', 'farmers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', MilkCollection::class);

        $validated = $request->validate([
            'farmer_id' => [
                'required',
                Rule::exists('farmers', 'id')->where('status', true)
            ],
            'collection_date' => ['required', 'date'],
            'shift' => ['required', 'string', 'in:morning,evening'],
            'milk_quantity' => ['required', 'numeric', 'gt:0'],
            'fat' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'snf' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'rate' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Server-side duplicate prevention check
        $duplicate = MilkCollection::where('farmer_id', $validated['farmer_id'])
            ->whereDate('collection_date', $validated['collection_date'])
            ->where('shift', $validated['shift'])
            ->exists();

        if ($duplicate) {
            return back()->withErrors([
                'farmer_id' => 'A milk collection entry already exists for this farmer on the selected date and shift.'
            ])->withInput();
        }

        // Server-side amount calculation
        $validated['amount'] = $validated['milk_quantity'] * $validated['rate'];

        MilkCollection::create($validated);

        return redirect()->route('milk-collections.index')
            ->with('success', 'Milk collection entry recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MilkCollection $milkCollection): View
    {
        Gate::authorize('view', $milkCollection);

        $milkCollection->load(['farmer.village']);

        return view('milk_collections.show', compact('milkCollection'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MilkCollection $milkCollection): View
    {
        Gate::authorize('update', $milkCollection);

        $milkCollection->load(['farmer.village']);

        // Load all villages and farmers to support historical records even if inactive
        $villages = Village::orderBy('name')->get();
        $farmers = Farmer::with('village')->orderBy('name')->get();

        return view('milk_collections.edit', compact('milkCollection', 'villages', 'farmers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MilkCollection $milkCollection): RedirectResponse
    {
        Gate::authorize('update', $milkCollection);

        $validated = $request->validate([
            'farmer_id' => [
                'required',
                Rule::exists('farmers', 'id') // Keep editing possible even if deactivated
            ],
            'collection_date' => ['required', 'date'],
            'shift' => ['required', 'string', 'in:morning,evening'],
            'milk_quantity' => ['required', 'numeric', 'gt:0'],
            'fat' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'snf' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'rate' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Duplicate prevention check (excluding the current collection entry id)
        $duplicate = MilkCollection::where('farmer_id', $validated['farmer_id'])
            ->whereDate('collection_date', $validated['collection_date'])
            ->where('shift', $validated['shift'])
            ->where('id', '!=', $milkCollection->id)
            ->exists();

        if ($duplicate) {
            return back()->withErrors([
                'farmer_id' => 'A milk collection entry already exists for this farmer on the selected date and shift.'
            ])->withInput();
        }

        // Server-side amount calculation
        $validated['amount'] = $validated['milk_quantity'] * $validated['rate'];

        $milkCollection->update($validated);

        return redirect()->route('milk-collections.index')
            ->with('success', 'Milk collection entry updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MilkCollection $milkCollection): RedirectResponse
    {
        Gate::authorize('delete', $milkCollection);

        $milkCollection->delete();

        return redirect()->route('milk-collections.index')
            ->with('success', 'Milk collection entry deleted successfully.');
    }
}
