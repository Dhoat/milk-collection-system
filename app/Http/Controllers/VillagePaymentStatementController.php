<?php

namespace App\Http\Controllers;

use App\Models\Village;
use App\Services\VillagePaymentStatementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class VillagePaymentStatementController extends Controller
{
    protected VillagePaymentStatementService $statementService;

    public function __construct(VillagePaymentStatementService $statementService)
    {
        $this->statementService = $statementService;
    }

    /**
     * Display the Village Payment Statement screen.
     */
    public function index(Request $request): View
    {
        $villages = Village::where('status', true)->orderBy('name')->get();

        $statement = null;
        $villageId = $request->input('village_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($request->filled(['village_id', 'start_date', 'end_date'])) {
            $request->validate([
                'village_id' => 'required|exists:villages,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ], [
                'end_date.after_or_equal' => __('End Date must be on or after Start Date.'),
            ]);

            $statement = $this->statementService->getStatement(
                (int) $villageId,
                (string) $startDate,
                (string) $endDate
            );
        }

        return view('village_payment_statements.index', compact(
            'villages',
            'statement',
            'villageId',
            'startDate',
            'endDate'
        ));
    }

    /**
     * API Endpoint: Get Village Payment Statement JSON response.
     */
    public function apiStatement(Request $request): JsonResponse
    {
        $request->validate([
            'village_id' => 'required|exists:villages,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ], [
            'end_date.after_or_equal' => __('End Date must be on or after Start Date.'),
        ]);

        $statement = $this->statementService->getStatement(
            (int) $request->village_id,
            (string) $request->start_date,
            (string) $request->end_date
        );

        return response()->json($statement);
    }

    /**
     * Display full screen printable view for browser print.
     */
    public function printStatement(Request $request): View
    {
        $request->validate([
            'village_id' => 'required|exists:villages,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $statement = $this->statementService->getStatement(
            (int) $request->village_id,
            (string) $request->start_date,
            (string) $request->end_date
        );

        return view('village_payment_statements.print', compact('statement'));
    }

    /**
     * Download or stream PDF statement.
     */
    public function exportPdf(Request $request): Response|View
    {
        $request->validate([
            'village_id' => 'required|exists:villages,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $statement = $this->statementService->getStatement(
            (int) $request->village_id,
            (string) $request->start_date,
            (string) $request->end_date
        );

        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('village_payment_statements.pdf', compact('statement'))
                ->setPaper('a4', 'portrait');
            
            $filename = 'Village_Statement_' . str_replace(' ', '_', $statement['village']['name']) . '_' . $statement['period']['start_date'] . '_to_' . $statement['period']['end_date'] . '.pdf';
            
            return $pdf->download($filename);
        }

        return view('village_payment_statements.pdf', compact('statement'));
    }
}
