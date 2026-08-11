<?php

namespace Database\Seeders;

use App\Models\Farmer;
use App\Models\MilkCollection;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MilkCollectionSeeder extends Seeder
{
    /**
     * Seed realistic daily milk collection logs across the past 30 days.
     */
    public function run(): void
    {
        $farmers = Farmer::with('village')->where('status', true)->get();

        if ($farmers->isEmpty()) {
            return;
        }

        $startDate = Carbon::today()->subDays(30);
        $endDate = Carbon::today();

        // Loop over each day in the 30-day window
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $formattedDate = $date->format('Y-m-d');

            // Pick 30-40 farmers each day for realistic daily collection volume
            $dailyFarmers = $farmers->shuffle()->take(35);

            foreach ($dailyFarmers as $farmer) {
                foreach (['morning', 'evening'] as $shift) {
                    $quantity = round(rand(80, 220) / 10, 1); // 8.0 to 22.0 Litres
                    $fat = round(rand(42, 62) / 10, 1);      // 4.2 to 6.2 %
                    $snf = round(rand(83, 91) / 10, 1);      // 8.3 to 9.1 %
                    $rate = round(40 + ($fat * 1.5) + ($snf * 0.8), 2); // Dynamic rate based on FAT/SNF
                    $amount = round($quantity * $rate, 2);

                    MilkCollection::updateOrCreate(
                        [
                            'farmer_id' => $farmer->id,
                            'collection_date' => $formattedDate,
                            'shift' => $shift,
                        ],
                        [
                            'milk_quantity' => $quantity,
                            'fat' => $fat,
                            'snf' => $snf,
                            'rate' => $rate,
                            'amount' => $amount,
                            'notes' => 'Routine collection entry',
                        ]
                    );
                }
            }
        }
    }
}
