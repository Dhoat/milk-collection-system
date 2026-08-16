<?php

namespace App\Services;

use App\Models\MilkReceiving;
use App\Models\Village;
use Carbon\Carbon;

class VillagePaymentStatementService
{
    /**
     * Generate Village-wise Milk Payment Statement data for a selected village and date range.
     *
     * @param int $villageId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getStatement(int $villageId, string $startDate, string $endDate): array
    {
        $village = Village::findOrFail($villageId);

        $parsedStart = Carbon::parse($startDate)->startOfDay();
        $parsedEnd = Carbon::parse($endDate)->endOfDay();

        // Query receiving records database-level
        $receivings = MilkReceiving::where('village_id', $villageId)
            ->whereBetween('receiving_date', [$parsedStart->toDateString(), $parsedEnd->toDateString()])
            ->orderBy('receiving_date', 'asc')
            ->orderByRaw("CASE WHEN shift = 'morning' THEN 1 WHEN shift = 'evening' THEN 2 ELSE 3 END")
            ->orderBy('id', 'asc')
            ->get();

        $entries = [];
        $runningBalance = 0.00;
        $totalMilk = 0.00;
        $totalPayment = 0.00;
        $totalFatVolume = 0.00;
        $totalSnfVolume = 0.00;

        foreach ($receivings as $receiving) {
            $qty = (float) ($receiving->received_quantity ?? $receiving->expected_quantity ?? 0);
            $fat = (float) ($receiving->received_fat ?? $receiving->expected_fat ?? 0);
            $snf = (float) ($receiving->received_snf ?? $receiving->expected_snf ?? 0);

            // Determine price using existing Fat/SNF rate formula: 40 + (fat * 1.5) + (snf * 0.8)
            $price = round(40 + ($fat * 1.5) + ($snf * 0.8), 2);
            $payment = round($qty * $price, 2);

            $runningBalance = round($runningBalance + $payment, 2);

            $totalMilk += $qty;
            $totalPayment += $payment;
            $totalFatVolume += ($qty * $fat);
            $totalSnfVolume += ($qty * $snf);

            $shiftLabel = ucfirst($receiving->shift ?? 'Receiving');

            $entries[] = [
                'id' => $receiving->id,
                'date' => Carbon::parse($receiving->receiving_date)->format('Y-m-d'),
                'date_formatted' => Carbon::parse($receiving->receiving_date)->format('d-m-Y'),
                'particular' => "Receiving ({$shiftLabel})",
                'shift' => $receiving->shift,
                'milk_quantity' => round($qty, 2),
                'fat' => round($fat, 2),
                'snf' => round($snf, 2),
                'price' => round($price, 2),
                'payment' => round($payment, 2),
                'running_balance' => round($runningBalance, 2),
            ];
        }

        $totalMilk = round($totalMilk, 2);
        $totalPayment = round($totalPayment, 2);
        $avgFat = $totalMilk > 0 ? round($totalFatVolume / $totalMilk, 2) : 0.00;
        $avgSnf = $totalMilk > 0 ? round($totalSnfVolume / $totalMilk, 2) : 0.00;
        $closingBalance = round($runningBalance, 2);

        return [
            'village' => [
                'id' => $village->id,
                'name' => $village->name,
                'code' => $village->code,
            ],
            'period' => [
                'start_date' => Carbon::parse($startDate)->format('Y-m-d'),
                'end_date' => Carbon::parse($endDate)->format('Y-m-d'),
                'start_date_formatted' => Carbon::parse($startDate)->format('d-m-Y'),
                'end_date_formatted' => Carbon::parse($endDate)->format('d-m-Y'),
            ],
            'entries' => $entries,
            'summary' => [
                'total_milk' => $totalMilk,
                'average_fat' => $avgFat,
                'average_snf' => $avgSnf,
                'total_payment' => $totalPayment,
                'closing_balance' => $closingBalance,
            ],
        ];
    }
}
