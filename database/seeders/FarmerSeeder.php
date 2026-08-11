<?php

namespace Database\Seeders;

use App\Models\Farmer;
use App\Models\Village;
use Illuminate\Database\Seeder;

class FarmerSeeder extends Seeder
{
    /**
     * Seed realistic Punjabi farmers across all seeded villages.
     */
    public function run(): void
    {
        $punjabiNames = [
            ['name' => 'ਗੁਰਪ੍ਰੀਤ ਸਿੰਘ', 'father' => 'ਗੁਰਮੁਖ ਸਿੰਘ'],
            ['name' => 'ਜਸਵਿੰਦਰ ਸਿੰਘ', 'father' => 'ਕਰਨੈਲ ਸਿੰਘ'],
            ['name' => 'ਹਰਜੀਤ ਸਿੰਘ', 'father' => 'ਭਜਨ ਸਿੰਘ'],
            ['name' => 'ਸੁਖਵਿੰਦਰ ਸਿੰਘ', 'father' => 'ਬਲਦੇਵ ਸਿੰਘ'],
            ['name' => 'ਮਨਦੀਪ ਸਿੰਘ', 'father' => 'ਦਰਸ਼ਨ ਸਿੰਘ'],
            ['name' => 'ਕੁਲਦੀਪ ਸਿੰਘ', 'father' => 'ਹਰਬੰਸ ਸਿੰਘ'],
            ['name' => 'ਬਲਵਿੰਦਰ ਸਿੰਘ', 'father' => 'ਸ਼ਿੰਗਾਰਾ ਸਿੰਘ'],
            ['name' => 'ਪਰਮਜੀਤ ਸਿੰਘ', 'father' => 'ਸੋਹਣ ਸਿੰਘ'],
            ['name' => 'ਅਮਰਜੀਤ ਸਿੰਘ', 'father' => 'ਮਹਿੰਦਰ ਸਿੰਘ'],
            ['name' => 'ਗੁਰਮੇਲ ਸਿੰਘ', 'father' => 'ਚੰਦ ਸਿੰਘ'],
            ['name' => 'ਜਗਸੀਰ ਸਿੰਘ', 'father' => 'ਬਚਨ ਸਿੰਘ'],
            ['name' => 'ਲਖਵਿੰਦਰ ਸਿੰਘ', 'father' => 'ਪ੍ਰੀਤਮ ਸਿੰਘ'],
            ['name' => 'ਸੁਖਦੇਵ ਸਿੰਘ', 'father' => 'ਜੰਗ ਸਿੰਘ'],
            ['name' => 'ਰਣਜੀਤ ਸਿੰਘ', 'father' => 'ਮਲਕੀਤ ਸਿੰਘ'],
            ['name' => 'ਹਰਪ੍ਰੀਤ ਸਿੰਘ', 'father' => 'ਸਾਧੂ ਸਿੰਘ'],
            ['name' => 'ਗੁਰਚਰਨ ਸਿੰਘ', 'father' => 'ਅਜਾਇਬ ਸਿੰਘ'],
            ['name' => 'ਬਲਜੀਤ ਸਿੰਘ', 'father' => 'ਨੱਥਾ ਸਿੰਘ'],
            ['name' => 'ਜਸਪਾਲ ਸਿੰਘ', 'father' => 'ਗੁਰਦਿਆਲ ਸਿੰਘ'],
            ['name' => 'ਦਵਿੰਦਰ ਸਿੰਘ', 'father' => 'ਹਰਨੇਕ ਸਿੰਘ'],
            ['name' => 'ਨਿਰਮਲ ਸਿੰਘ', 'father' => 'ਸੁਰਜੀਤ ਸਿੰਘ'],
        ];

        $villages = Village::all();

        if ($villages->isEmpty()) {
            return;
        }

        foreach ($villages as $vIndex => $village) {
            // Seed 10 farmers per village
            for ($i = 1; $i <= 10; $i++) {
                $namePair = $punjabiNames[($vIndex * 2 + $i) % count($punjabiNames)];
                $farmerCode = sprintf("FRM-%s-%02d", $village->code, $i);
                $mobile = sprintf("987%02d%05d", $village->id, $i * 111);

                Farmer::updateOrCreate(
                    ['farmer_code' => $farmerCode],
                    [
                        'village_id' => $village->id,
                        'name' => $namePair['name'],
                        'father_name' => $namePair['father'],
                        'mobile' => $mobile,
                        'alternate_mobile' => null,
                        'address' => "House No. {$i}, {$village->name}",
                        'gender' => 'male',
                        'joining_date' => now()->subMonths(rand(1, 12))->format('Y-m-d'),
                        'bank_name' => 'Punjab National Bank',
                        'account_number' => sprintf("30590001000%02d%02d", $village->id, $i),
                        'ifsc_code' => 'PUNB0305900',
                        'status' => true,
                    ]
                );
            }
        }
    }
}
