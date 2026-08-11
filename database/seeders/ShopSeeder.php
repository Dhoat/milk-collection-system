<?php

namespace Database\Seeders;

use App\Models\Shop;
use App\Models\Village;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    /**
     * Seed realistic retail shops around Malerkotla & Ahmedgarh.
     */
    public function run(): void
    {
        $villages = Village::all()->keyBy('code');

        $shops = [
            [
                'shop_code' => 'SHP-001',
                'name' => 'Malerkotla Central Dairy Store',
                'owner_name' => 'ਮੋਹਨ ਲਾਲ',
                'phone' => '9872011001',
                'email' => 'malerkotla.dairy@gmail.com',
                'address' => 'Shop No. 12, Main Bazaar, Malerkotla',
                'area' => 'Main Market',
                'village_code' => 'VIL-001',
                'status' => true,
                'credit_limit' => 50000.00,
                'notes' => 'Key wholesale retail partner',
            ],
            [
                'shop_code' => 'SHP-002',
                'name' => 'Kanganwal Milk Booth',
                'owner_name' => 'ਰਾਜੇਸ਼ ਕੁਮਾਰ',
                'phone' => '9872011002',
                'email' => 'kanganwal.milk@gmail.com',
                'address' => 'Bus Stand Road, Kanganwal',
                'area' => 'Bus Stand Area',
                'village_code' => 'VIL-004',
                'status' => true,
                'credit_limit' => 35000.00,
                'notes' => 'High volume morning retail outlet',
            ],
            [
                'shop_code' => 'SHP-003',
                'name' => 'Jarg Sweet Shop & Dairy',
                'owner_name' => 'ਅਮਰਜੀਤ ਸਿੰਘ',
                'phone' => '9872011003',
                'email' => 'jargsweets@gmail.com',
                'address' => 'Main Chowk, Jarg Village',
                'area' => 'Village Square',
                'village_code' => 'VIL-005',
                'status' => true,
                'credit_limit' => 60000.00,
                'notes' => 'Regular purchaser of raw milk & paneer',
            ],
            [
                'shop_code' => 'SHP-004',
                'name' => 'Kup Kalan Dairy Store',
                'owner_name' => 'ਗੁਰਮੇਲ ਸਿੰਘ',
                'phone' => '9872011004',
                'email' => 'kupkalan.dairy@gmail.com',
                'address' => 'Highway Sector, Kup Kalan',
                'area' => 'Highway Commercial',
                'village_code' => 'VIL-006',
                'status' => true,
                'credit_limit' => 40000.00,
                'notes' => 'Daily pouch milk retail partner',
            ],
            [
                'shop_code' => 'SHP-005',
                'name' => 'Bhogiwal Dairy Point',
                'owner_name' => 'ਸੁਖਦੇਵ ਸਿੰਘ',
                'phone' => '9872011005',
                'email' => 'bhogiwal.dp@gmail.com',
                'address' => 'Link Road, Bhogiwal',
                'area' => 'North Sector',
                'village_code' => 'VIL-003',
                'status' => true,
                'credit_limit' => 30000.00,
                'notes' => 'Active retail partner',
            ],
            [
                'shop_code' => 'SHP-006',
                'name' => 'Ahmedgarh Fresh Milk Hub',
                'owner_name' => 'ਸੰਜੀਵ ਕੁਮਾਰ',
                'phone' => '9872011006',
                'email' => 'ahmedgarh.fresh@gmail.com',
                'address' => 'Railway Road, Ahmedgarh',
                'area' => 'Station Road',
                'village_code' => 'VIL-008',
                'status' => true,
                'credit_limit' => 75000.00,
                'notes' => 'Major city distribution center',
            ],
            [
                'shop_code' => 'SHP-007',
                'name' => 'Binjoki Retail Dairy',
                'owner_name' => 'ਦਵਿੰਦਰ ਸਿੰਘ',
                'phone' => '9872011007',
                'email' => null,
                'address' => 'Cooperative Road, Binjoki Khurd',
                'area' => 'East Zone',
                'village_code' => 'VIL-002',
                'status' => true,
                'credit_limit' => 25000.00,
                'notes' => 'Local village retailer',
            ],
            [
                'shop_code' => 'SHP-008',
                'name' => 'Kup Khurd Dairy Corner',
                'owner_name' => 'ਬਲਦੇਵ ਸਿੰਘ',
                'phone' => '9872011008',
                'email' => null,
                'address' => 'Gurdwara Road, Kup Khurd',
                'area' => 'Central Market',
                'village_code' => 'VIL-007',
                'status' => true,
                'credit_limit' => 20000.00,
                'notes' => 'Small retail booth',
            ],
            [
                'shop_code' => 'SHP-009',
                'name' => 'Dehliz Milk Distribution',
                'owner_name' => 'ਜਸਵੰਤ ਸਿੰਘ',
                'phone' => '9872011009',
                'email' => 'dehliz.distributors@gmail.com',
                'address' => 'Main Market, Dehliz Kalan',
                'area' => 'Feeder Market',
                'village_code' => 'VIL-011',
                'status' => true,
                'credit_limit' => 45000.00,
                'notes' => 'Bulk distributor',
            ],
            [
                'shop_code' => 'SHP-010',
                'name' => 'Maholi Sweets & Dairy',
                'owner_name' => 'ਸਤਪਾਲ ਸਿੰਘ',
                'phone' => '9872011110',
                'email' => null,
                'address' => 'Main Market, Maholi Khurd',
                'area' => 'Retail Belt',
                'village_code' => 'VIL-009',
                'status' => true,
                'credit_limit' => 30000.00,
                'notes' => 'Sweet shop partner',
            ],
            [
                'shop_code' => 'SHP-011',
                'name' => 'Himmatana Dairy Hub',
                'owner_name' => 'ਹਰਬੰਸ ਸਿੰਘ',
                'phone' => '9872011111',
                'email' => null,
                'address' => 'Dairy Belt, Himmatana',
                'area' => 'West Zone',
                'village_code' => 'VIL-010',
                'status' => true,
                'credit_limit' => 25000.00,
                'notes' => 'Local village booth',
            ],
            [
                'shop_code' => 'SHP-012',
                'name' => 'Dehliz Khurd Fresh Dairy',
                'owner_name' => 'ਮਨਜੀਤ ਸਿੰਘ',
                'phone' => '9872011112',
                'email' => null,
                'address' => 'Link Road, Dehliz Khurd',
                'area' => 'South Sector',
                'village_code' => 'VIL-012',
                'status' => true,
                'credit_limit' => 20000.00,
                'notes' => 'Retail partner',
            ],
        ];

        foreach ($shops as $shopData) {
            $vCode = $shopData['village_code'];
            unset($shopData['village_code']);
            $shopData['village_id'] = $villages[$vCode]->id ?? null;

            Shop::updateOrCreate(
                ['shop_code' => $shopData['shop_code']],
                $shopData
            );
        }
    }
}
