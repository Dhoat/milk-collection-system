<?php

namespace App\Http\Controllers;

use App\Models\Village;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VillageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $villages = Village::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('villages.index', compact('villages', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('villages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:villages,code'],
            'address' => ['nullable', 'string', 'max:1000'],
            'status' => ['boolean'],
        ]);

        // Default status to true (active) if not provided (e.g. checkbox unchecked)
        $validated['status'] = $request->has('status');

        Village::create($validated);

        return redirect()->route('villages.index')
            ->with('success', 'Village created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Village $village): View
    {
        return view('villages.show', compact('village'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Village $village): View
    {
        return view('villages.edit', compact('village'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Village $village): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('villages', 'code')->ignore($village->id),
            ],
            'address' => ['nullable', 'string', 'max:1000'],
            'status' => ['boolean'],
        ]);

        $validated['status'] = $request->has('status');

        $village->update($validated);

        return redirect()->route('villages.index')
            ->with('success', 'Village updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Village $village): RedirectResponse
    {
        $village->delete();

        return redirect()->route('villages.index')
            ->with('success', 'Village deleted successfully.');
    }
}
