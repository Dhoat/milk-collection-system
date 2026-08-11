<?php

namespace Database\Seeders;

use App\Models\Delivery;
use App\Models\ShopOrder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DeliverySeeder extends Seeder
{
    /**
     * Seed realistic delivery records linked to shop orders.
     */
    public function run(): void
    {
        $orders = ShopOrder::with('shop')->whereIn('status', ['confirmed', 'preparing', 'dispatched', 'delivered'])->get();
        $deliveryStaff = User::where('role', 'center_staff')->first() ?? User::first();
        $creator = User::where('role', 'manager')->first() ?? User::first();

        if ($orders->isEmpty()) {
            return;
        }

        $deliveryCounter = 1;

        foreach ($orders as $order) {
            $existingDelivery = Delivery::where('shop_order_id', $order->id)->first();
            $deliveryNumber = $existingDelivery
                ? $existingDelivery->delivery_number
                : Delivery::generateDeliveryNumber();

            $orderDate = Carbon::parse($order->order_date);

            // Determine status based on shop order status
            if ($order->status === 'delivered') {
                $status = 'delivered';
                $dispatchedAt = $orderDate->copy()->addHours(2);
                $deliveredAt = $orderDate->copy()->addHours(5);
            } elseif ($order->status === 'dispatched') {
                $status = 'out_for_delivery';
                $dispatchedAt = $orderDate->copy()->addHours(2);
                $deliveredAt = null;
            } elseif ($order->status === 'preparing') {
                $status = 'assigned';
                $dispatchedAt = null;
                $deliveredAt = null;
            } else {
                $status = 'pending';
                $dispatchedAt = null;
                $deliveredAt = null;
            }

            Delivery::updateOrCreate(
                ['shop_order_id' => $order->id],
                [
                    'delivery_number' => $deliveryNumber,
                    'shop_id' => $order->shop_id,
                    'delivery_date' => $order->order_date,
                    'status' => $status,
                    'delivery_address' => $order->shop->address ?? 'Main Village Outlet',
                    'contact_person' => $order->shop->owner_name ?? 'Store Manager',
                    'contact_phone' => $order->shop->phone ?? '9872000000',
                    'assigned_to' => $deliveryStaff->id,
                    'created_by' => $creator->id,
                    'notes' => 'Route dispatch delivery schedule',
                    'dispatched_at' => $dispatchedAt,
                    'delivered_at' => $deliveredAt,
                ]
            );
        }
    }
}
