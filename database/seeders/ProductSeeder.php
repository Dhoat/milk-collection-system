<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seed realistic dairy products.
     */
    public function run(): void
    {
        $products = [
            [
                'product_code' => 'SKU-MILK-RAW',
                'name' => 'Raw Fresh Milk (Whole)',
                'category' => 'raw_milk',
                'unit' => 'Litre',
                'unit_price' => 55.00,
                'stock_quantity' => 0, // Dynamic stock read from MilkStock
                'status' => true,
                'notes' => 'Bulk unprocessed fresh farm milk',
            ],
            [
                'product_code' => 'SKU-MILK-TONED',
                'name' => 'Pasteurized Toned Milk 1L',
                'category' => 'dairy',
                'unit' => 'Litre',
                'unit_price' => 60.00,
                'stock_quantity' => 500,
                'status' => true,
                'notes' => 'Pouch packed 3.0% FAT toned milk',
            ],
            [
                'product_code' => 'SKU-MILK-FULL',
                'name' => 'Full Cream Milk 1L',
                'category' => 'dairy',
                'unit' => 'Litre',
                'unit_price' => 66.00,
                'stock_quantity' => 450,
                'status' => true,
                'notes' => '6.0% FAT rich full cream milk',
            ],
            [
                'product_code' => 'SKU-DAHI-500G',
                'name' => 'Fresh Farm Dahi (Curd) 500g',
                'category' => 'dairy',
                'unit' => 'Pouch',
                'unit_price' => 35.00,
                'stock_quantity' => 250,
                'status' => true,
                'notes' => 'Traditional thick set curd',
            ],
            [
                'product_code' => 'SKU-GHEE-1L',
                'name' => 'Pure Desi Ghee 1L Jar',
                'category' => 'ghee',
                'unit' => 'Jar',
                'unit_price' => 650.00,
                'stock_quantity' => 120,
                'status' => true,
                'notes' => 'Aromatic granual buffalo desi ghee',
            ],
            [
                'product_code' => 'SKU-PANEER-500G',
                'name' => 'Fresh Malai Paneer 500g',
                'category' => 'cheese',
                'unit' => 'Packet',
                'unit_price' => 190.00,
                'stock_quantity' => 180,
                'status' => true,
                'notes' => 'Soft hygienic fresh cottage cheese',
            ],
            [
                'product_code' => 'SKU-LASSI-300ML',
                'name' => 'Sweet Punjabi Lassi 300ml',
                'category' => 'beverage',
                'unit' => 'Bottle',
                'unit_price' => 30.00,
                'stock_quantity' => 350,
                'status' => true,
                'notes' => 'Refreshing traditional sweet drink',
            ],
            [
                'product_code' => 'SKU-BUTTERMILK-500ML',
                'name' => 'Spiced Masala Chach 500ml',
                'category' => 'beverage',
                'unit' => 'Pouch',
                'unit_price' => 20.00,
                'stock_quantity' => 300,
                'status' => true,
                'notes' => 'Digestive salted buttermilk',
            ],
        ];

        foreach ($products as $productData) {
            Product::updateOrCreate(
                ['product_code' => $productData['product_code']],
                $productData
            );
        }
    }
}
