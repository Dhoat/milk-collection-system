<?php

namespace Database\Seeders;

use App\Models\Village;
use Illuminate\Database\Seeder;

class VillageSeeder extends Seeder
{
    /**
     * Seed realistic Malerkotla / Ahmedgarh villages.
     */
    public function run(): void
    {
        $villages = [
            ['name' => 'Binjoki Kalan', 'code' => 'VIL-001', 'address' => 'Binjoki Kalan, Malerkotla Tehsil, Sangrur District', 'status' => true],
            ['name' => 'Binjoki Khurd', 'code' => 'VIL-002', 'address' => 'Binjoki Khurd, Malerkotla Tehsil, Sangrur District', 'status' => true],
            ['name' => 'Bhogiwal', 'code' => 'VIL-003', 'address' => 'Bhogiwal Road, Malerkotla Region, Punjab', 'status' => true],
            ['name' => 'Kanganwal', 'code' => 'VIL-004', 'address' => 'Kanganwal Bypass, Malerkotla, Punjab', 'status' => true],
            ['name' => 'Jarg', 'code' => 'VIL-005', 'address' => 'Jarg Village Hub, Khanna-Malerkotla Road, Punjab', 'status' => true],
            ['name' => 'Kup Kalan', 'code' => 'VIL-006', 'address' => 'Kup Kalan Highway Sector, Ahmedgarh Tehsil', 'status' => true],
            ['name' => 'Kup Khurd', 'code' => 'VIL-007', 'address' => 'Kup Khurd Link Road, Ahmedgarh Tehsil', 'status' => true],
            ['name' => 'Maholi Kalan', 'code' => 'VIL-008', 'address' => 'Maholi Kalan Agricultural Zone, Ahmedgarh', 'status' => true],
            ['name' => 'Maholi Khurd', 'code' => 'VIL-009', 'address' => 'Maholi Khurd Village, Ahmedgarh, Punjab', 'status' => true],
            ['name' => 'Himmatana', 'code' => 'VIL-010', 'address' => 'Himmatana Dairy Belt, Malerkotla Tehsil', 'status' => true],
            ['name' => 'Dehliz Kalan', 'code' => 'VIL-011', 'address' => 'Dehliz Kalan Main Road, Ahmedgarh Tehsil', 'status' => true],
            ['name' => 'Dehliz Khurd', 'code' => 'VIL-012', 'address' => 'Dehliz Khurd Feeder Road, Ahmedgarh Tehsil', 'status' => true],
        ];

        foreach ($villages as $villageData) {
            Village::updateOrCreate(
                ['code' => $villageData['code']],
                $villageData
            );
        }
    }
}
