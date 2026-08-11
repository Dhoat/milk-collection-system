<?php

namespace Database\Seeders;

use App\Models\MilkCollection;
use App\Models\MilkReceiving;
use App\Models\User;
use App\Models\Village;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MilkReceivingSeeder extends Seeder
{
    /**
     * Seed Main Milk Center receiving records based on village collections.
     * Setting status to 'confirmed' automatically triggers MilkStock IN creation.
     */
    public function run(): void
    {
        $villages = Village::where('status', true)->get();
        $centerStaff = User::where('role', 'center_staff')->first() ?? User::first();

        if ($villages->isEmpty()) {
            return;
        }

        $startDate = Carbon::today()->subDays(30);
        $endDate = Carbon::today();

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $formattedDate = $date->format('Y-m-d');

            foreach ($villages as $village) {
                foreach (['morning', 'evening'] as $shift) {
                    // Aggregate collections for this village on this date & shift
                    $collections = MilkCollection::whereHas('farmer', function ($q) use ($village) {
                        $q->where('village_id', $village->id);
                    })
                    ->whereDate('collection_date', $formattedDate)
                    ->where('shift', $shift)
                    ->get();

                    if ($collections->isEmpty()) {
                        continue;
                    }

                    $expectedQty = round($collections->sum('milk_quantity'), 2);
                    $avgFat = round($collections->avg('fat') ?? 4.8, 2);
                    $avgSnf = round($collections->avg('snf') ?? 8.5, 2);

                    // Received quantity with slight realistic transport loss (98% - 100%)
                    $receivedQty = round($expectedQty * (rand(985, 1000) / 1000), 2);
                    $receivedFat = round($avgFat * (rand(99, 100) / 100), 2);
                    $receivedSnf = round($avgSnf * (rand(99, 100) / 100), 2);

                    // updateOrCreate will save the model, triggering MilkReceiving::booted()
                    // which automatically creates/syncs the Stock IN record in MilkStock table!
                    MilkReceiving::updateOrCreate(
                        [
                            'village_id' => $village->id,
                            'receiving_date' => $formattedDate,
                            'shift' => $shift,
                        ],
                        [
                            'expected_quantity' => $expectedQty,
                            'received_quantity' => $receivedQty,
                            'expected_fat' => $avgFat,
                            'received_fat' => $receivedFat,
                            'expected_snf' => $avgSnf,
                            'received_snf' => $receivedSnf,
                            'status' => 'confirmed',
                            'verified_by' => $centerStaff->id,
                            'notes' => "Main center milk receiving from {$village->name}",
                        ]
                    );
                }
            }
        }
    }
}
