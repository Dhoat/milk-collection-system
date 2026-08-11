<?php

namespace App\Http\Controllers;

use App\Models\MilkStock;
use App\Services\MilkStockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class MilkStockController extends Controller
{
    /**
     * Display the stock dashboard and transaction history ledger.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', MilkStock::class);

        $filterDate = $request->input('date');
        $filterType = $request->input('type');
        $search = $request->input('search');

        $query = MilkStock::with(['milkReceiving.village', 'creator'])->orderBy('transaction_date', 'desc')->orderBy('id', 'desc');

        if ($filterDate) {
            $query->whereDate('transaction_date', $filterDate);
        }

        if ($filterType && in_array($filterType, ['in', 'out'])) {
            $query->where('type', $filterType);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('source_or_reason', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $transactions = $query->paginate(15)->withQueryString();

        // Calculate KPI summary metrics
        $openingStock = MilkStock::getOpeningStock($filterDate);
        $todayReceived = MilkStock::getTodayReceived($filterDate);
        $todayStockOut = MilkStock::getTodayStockOut($filterDate);
        $availableStock = MilkStock::getAvailableStock();

        // Compute running balances for displayed transactions
        // Cumulative balance = total stock up to that specific transaction
        $allSorted = MilkStock::orderBy('transaction_date', 'asc')->orderBy('id', 'asc')->get();
        $balanceMap = [];
        $currentBalance = 0;
        foreach ($allSorted as $item) {
            if ($item->type === 'in') {
                $currentBalance += $item->quantity;
            } else {
                $currentBalance -= $item->quantity;
            }
            $balanceMap[$item->id] = round($currentBalance, 2);
        }

        return view('milk_stocks.index', compact(
            'transactions',
            'openingStock',
            'todayReceived',
            'todayStockOut',
            'availableStock',
            'balanceMap',
            'filterDate',
            'filterType',
            'search'
        ));
    }

    /**
     * Store a manual Stock OUT transaction.
     */
    public function storeOut(Request $request, MilkStockService $stockService): RedirectResponse
    {
        Gate::authorize('createOut', MilkStock::class);

        $request->validate([
            'transaction_date' => 'required|date',
            'quantity' => 'required|numeric|gt:0',
            'source_or_reason' => 'required|string|max:255',
            'fat' => 'nullable|numeric|min:0|max:100',
            'snf' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        $available = MilkStock::getAvailableStock();
        if ((float)$request->quantity > $available) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['quantity' => sprintf(
                    __('Stock OUT quantity (%.2f L) cannot exceed current available stock (%.2f L).'),
                    (float)$request->quantity,
                    $available
                )]);
        }

        try {
            $stockService->recordStockOut($request->all(), auth()->id());

            return redirect()->route('milk-stocks.index')
                ->with('success', __('Stock OUT transaction recorded successfully. Available stock updated.'));
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withInput()->withErrors(['quantity' => $e->getMessage()]);
        }
    }
}
