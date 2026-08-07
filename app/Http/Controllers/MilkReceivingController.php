<?php

namespace App\Http\Controllers;

use App\Models\MilkCollection;
use App\Models\MilkReceiving;
use App\Models\Village;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MilkReceivingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', MilkReceiving::class);

        $query = MilkReceiving::with(['village', 'verifier'])->latest('receiving_date');

        // Apply filters
        if ($request->filled('date')) {
            $query->whereDate('receiving_date', $request->date);
        }

        if ($request->filled('village_id')) {
            $query->where('village_id', $request->village_id);
        }

        if ($request->filled('shift')) {
            $query->where('shift', $request->shift);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $receivings = $query->paginate(10)->withQueryString();
        $villages = Village::where('status', true)->orderBy('name')->get();

        return view('milk_receivings.index', compact('receivings', 'villages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', MilkReceiving::class);

        $villages = Village::where('status', true)->orderBy('name')->get();
        return view('milk_receivings.create', compact('villages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', MilkReceiving::class);

        $request->validate([
            'village_id' => [
                'required',
                'exists:villages,id',
                Rule::unique('milk_receivings')->where(function ($query) use ($request) {
                    return $query->where('receiving_date', $request->receiving_date)
                                 ->where('shift', $request->shift);
                })
            ],
            'receiving_date' => 'required|date',
            'shift' => 'required|in:morning,evening',
            'received_quantity' => 'required|numeric|min:0',
            'received_fat' => 'nullable|numeric|min:0|max:100',
            'received_snf' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ], [
            'village_id.unique' => __('A receiving record already exists for this village, date, and shift.')
        ]);

        // Calculate expected metrics from farmer collections
        $metrics = $this->calculateExpectedMetrics(
            $request->village_id,
            $request->receiving_date,
            $request->shift
        );

        // Determine status automatically based on tolerance (e.g. discrepancy if variance > 0.1 Liters)
        $qtyDiff = abs((float)$request->received_quantity - $metrics['expected_quantity']);
        $status = $qtyDiff > 0.1 ? 'discrepancy' : 'received';

        MilkReceiving::create([
            'village_id' => $request->village_id,
            'receiving_date' => $request->receiving_date,
            'shift' => $request->shift,
            'expected_quantity' => $metrics['expected_quantity'],
            'received_quantity' => $request->received_quantity,
            'expected_fat' => $metrics['expected_fat'],
            'received_fat' => $request->received_fat,
            'expected_snf' => $metrics['expected_snf'],
            'received_snf' => $request->received_snf,
            'status' => $status,
            'verified_by' => auth()->id(),
            'notes' => $request->notes,
        ]);

        return redirect()->route('milk-receivings.index')
            ->with('success', __('Milk receiving record created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(MilkReceiving $milkReceiving): View
    {
        Gate::authorize('view', $milkReceiving);

        $milkReceiving->load(['village', 'verifier']);

        // Fetch the individual farmer collections for traceability
        $farmerCollections = MilkCollection::whereHas('farmer', function ($query) use ($milkReceiving) {
            $query->where('village_id', $milkReceiving->village_id);
        })
        ->whereDate('collection_date', $milkReceiving->receiving_date)
        ->where('shift', $milkReceiving->shift)
        ->with('farmer')
        ->latest()
        ->get();

        return view('milk_receivings.show', compact('milkReceiving', 'farmerCollections'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MilkReceiving $milkReceiving): View
    {
        Gate::authorize('update', $milkReceiving);

        $villages = Village::where('status', true)->orderBy('name')->get();
        return view('milk_receivings.edit', compact('milkReceiving', 'villages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MilkReceiving $milkReceiving): RedirectResponse
    {
        Gate::authorize('update', $milkReceiving);

        $request->validate([
            'village_id' => [
                'required',
                'exists:villages,id',
                Rule::unique('milk_receivings')->where(function ($query) use ($request, $milkReceiving) {
                    return $query->where('receiving_date', $request->receiving_date)
                                 ->where('shift', $request->shift);
                })->ignore($milkReceiving->id)
            ],
            'receiving_date' => 'required|date',
            'shift' => 'required|in:morning,evening',
            'received_quantity' => 'required|numeric|min:0',
            'received_fat' => 'nullable|numeric|min:0|max:100',
            'received_snf' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ], [
            'village_id.unique' => __('A receiving record already exists for this village, date, and shift.')
        ]);

        // Calculate expected metrics from farmer collections
        $metrics = $this->calculateExpectedMetrics(
            $request->village_id,
            $request->receiving_date,
            $request->shift
        );

        // Determine status automatically based on tolerance
        $qtyDiff = abs((float)$request->received_quantity - $metrics['expected_quantity']);
        $status = $qtyDiff > 0.1 ? 'discrepancy' : 'received';

        $milkReceiving->update([
            'village_id' => $request->village_id,
            'receiving_date' => $request->receiving_date,
            'shift' => $request->shift,
            'expected_quantity' => $metrics['expected_quantity'],
            'received_quantity' => $request->received_quantity,
            'expected_fat' => $metrics['expected_fat'],
            'received_fat' => $request->received_fat,
            'expected_snf' => $metrics['expected_snf'],
            'received_snf' => $request->received_snf,
            'status' => $status,
            'verified_by' => auth()->id(),
            'notes' => $request->notes,
        ]);

        return redirect()->route('milk-receivings.index')
            ->with('success', __('Milk receiving record updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MilkReceiving $milkReceiving): RedirectResponse
    {
        Gate::authorize('delete', $milkReceiving);

        $milkReceiving->delete();
        return redirect()->route('milk-receivings.index')
            ->with('success', __('Milk receiving record deleted successfully.'));
    }

    /**
     * API: Get collection summary aggregates for a selected village, date, and shift.
     */
    public function getCollectionSummary(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', MilkReceiving::class);
        $request->validate([
            'village_id' => 'required|exists:villages,id',
            'date' => 'required|date',
            'shift' => 'required|in:morning,evening',
        ]);

        $collections = MilkCollection::whereHas('farmer', function ($query) use ($request) {
            $query->where('village_id', $request->village_id);
        })
        ->whereDate('collection_date', $request->date)
        ->where('shift', $request->shift)
        ->get();

        $expectedQuantity = $collections->sum('milk_quantity');
        $expectedFat = 0;
        $expectedSnf = 0;

        if ($expectedQuantity > 0) {
            $totalFatVolume = $collections->sum(fn ($c) => $c->milk_quantity * ($c->fat ?? 0));
            $totalSnfVolume = $collections->sum(fn ($c) => $c->milk_quantity * ($c->snf ?? 0));
            $expectedFat = round($totalFatVolume / $expectedQuantity, 2);
            $expectedSnf = round($totalSnfVolume / $expectedQuantity, 2);
        }

        return response()->json([
            'expected_quantity' => round($expectedQuantity, 2),
            'expected_fat' => $expectedFat,
            'expected_snf' => $expectedSnf,
            'farmer_count' => $collections->count(),
        ]);
    }

    /**
     * Helper: Calculate expected metrics using weighted averages.
     */
    private function calculateExpectedMetrics(int $villageId, string $date, string $shift): array
    {
        $collections = MilkCollection::whereHas('farmer', function ($query) use ($villageId) {
            $query->where('village_id', $villageId);
        })
        ->whereDate('collection_date', $date)
        ->where('shift', $shift)
        ->get();

        $expectedQuantity = $collections->sum('milk_quantity');
        $expectedFat = null;
        $expectedSnf = null;

        if ($expectedQuantity > 0) {
            $totalFatVolume = $collections->sum(fn ($c) => $c->milk_quantity * ($c->fat ?? 0));
            $totalSnfVolume = $collections->sum(fn ($c) => $c->milk_quantity * ($c->snf ?? 0));
            $expectedFat = $totalFatVolume / $expectedQuantity;
            $expectedSnf = $totalSnfVolume / $expectedQuantity;
        }

        return [
            'expected_quantity' => $expectedQuantity,
            'expected_fat' => $expectedFat,
            'expected_snf' => $expectedSnf,
        ];
    }
}
