<?php

namespace App\Http\Controllers;

use App\Models\Farmer;
use App\Models\Village;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FarmerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Farmer::class);

        $search = $request->input('search');
        $villageId = $request->input('village_id');
        $status = $request->input('status');

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
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $villages = Village::orderBy('name')->get();

        return view('farmers.index', compact('farmers', 'villages', 'search', 'villageId', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', Farmer::class);

        // Only active villages can register new farmers
        $villages = Village::where('status', true)->orderBy('name')->get();

        return view('farmers.create', compact('villages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Farmer::class);

        $validated = $request->validate([
            'village_id' => ['required', 'exists:villages,id'],
            'farmer_code' => ['required', 'string', 'max:50', 'unique:farmers,farmer_code'],
            'name' => ['required', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'min:10', 'max:15', 'regex:/^[0-9+() -]{10,15}$/'],
            'alternate_mobile' => ['nullable', 'string', 'min:10', 'max:15', 'regex:/^[0-9+() -]{10,15}$/'],
            'address' => ['nullable', 'string', 'max:1000'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'joining_date' => ['nullable', 'date'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'ifsc_code' => ['nullable', 'string', 'max:20'],
            'status' => ['boolean'],
        ]);

        $validated['status'] = $request->has('status');

        Farmer::create($validated);

        return redirect()->route('farmers.index')
            ->with('success', 'Farmer registered successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Farmer $farmer): View
    {
        Gate::authorize('view', $farmer);

        $farmer->load('village');

        return view('farmers.show', compact('farmer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Farmer $farmer): View
    {
        Gate::authorize('update', $farmer);

        // Allow selection of active villages OR the farmer's current village (even if currently inactive)
        $villages = Village::where('status', true)
            ->orWhere('id', $farmer->village_id)
            ->orderBy('name')
            ->get();

        return view('farmers.edit', compact('farmer', 'villages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Farmer $farmer): RedirectResponse
    {
        Gate::authorize('update', $farmer);

        $validated = $request->validate([
            'village_id' => ['required', 'exists:villages,id'],
            'farmer_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('farmers', 'farmer_code')->ignore($farmer->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'min:10', 'max:15', 'regex:/^[0-9+() -]{10,15}$/'],
            'alternate_mobile' => ['nullable', 'string', 'min:10', 'max:15', 'regex:/^[0-9+() -]{10,15}$/'],
            'address' => ['nullable', 'string', 'max:1000'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'joining_date' => ['nullable', 'date'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'ifsc_code' => ['nullable', 'string', 'max:20'],
            'status' => ['boolean'],
        ]);

        $validated['status'] = $request->has('status');

        $farmer->update($validated);

        return redirect()->route('farmers.index')
            ->with('success', 'Farmer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Farmer $farmer): RedirectResponse
    {
        Gate::authorize('delete', $farmer);

        $farmer->delete();

        return redirect()->route('farmers.index')
            ->with('success', 'Farmer profile deleted successfully.');
    }
}
