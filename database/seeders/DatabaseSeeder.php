<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database in exact dependency order.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            VillageSeeder::class,
            FarmerSeeder::class,
            MilkCollectionSeeder::class,
            MilkReceivingSeeder::class,
            ProductSeeder::class,
            ShopSeeder::class,
            ShopOrderSeeder::class,
            DeliverySeeder::class,
        ]);
    }
}
