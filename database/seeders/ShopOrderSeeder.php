<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ShopOrderSeeder extends Seeder
{
    /**
     * Seed realistic shop orders and order items over the past 30 days.
     */
    public function run(): void
    {
        $shops = Shop::where('status', true)->get();
        $products = Product::where('status', true)->get();
        $creator = User::where('role', 'manager')->first() ?? User::first();

        if ($shops->isEmpty() || $products->isEmpty()) {
            return;
        }

        $statuses = ['delivered', 'delivered', 'dispatched', 'preparing', 'confirmed', 'pending'];
        $startDate = Carbon::today()->subDays(30);
        $endDate = Carbon::today();
        $orderCounter = 1;
        $shopCount = count($shops);

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDays(2)) {
            $formattedDate = $date->format('Y-m-d');
            // Select 2 shops deterministically per day
            $dayShops = [
                $shops[($orderCounter * 3) % $shopCount],
                $shops[($orderCounter * 5 + 1) % $shopCount],
            ];

            foreach ($dayShops as $shop) {
                $orderNumber = sprintf("ORD-2026-%04d", $orderCounter);
                $status = $statuses[$orderCounter % count($statuses)];
                $orderCounter++;

                // Create or update base order
                $order = ShopOrder::updateOrCreate(
                    ['order_number' => $orderNumber],
                    [
                        'shop_id' => $shop->id,
                        'order_date' => $formattedDate,
                        'status' => $status,
                        'subtotal' => 0,
                        'discount' => 0,
                        'total_amount' => 0,
                        'created_by' => $creator->id,
                        'notes' => 'Routine retail supply order',
                    ]
                );

                // Add 2 to 3 items per order deterministically
                $selectedProducts = [
                    $products[($orderCounter * 2) % count($products)],
                    $products[($orderCounter * 3 + 1) % count($products)],
                ];
                $subtotal = 0;

                // Clear existing items for idempotence
                $order->items()->delete();

                foreach ($selectedProducts as $product) {
                    $qty = rand(5, 30);
                    $price = $product->unit_price;
                    $lineTotal = round($qty * $price, 2);
                    $subtotal += $lineTotal;

                    ShopOrderItem::create([
                        'shop_order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $qty,
                        'unit' => $product->unit,
                        'unit_price' => $price,
                        'line_total' => $lineTotal,
                    ]);
                }

                $discount = rand(0, 1) ? round($subtotal * 0.02, 2) : 0; // 2% discount occasionally
                $totalAmount = round($subtotal - $discount, 2);

                $order->update([
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'total_amount' => $totalAmount,
                ]);
            }
        }
    }
}
