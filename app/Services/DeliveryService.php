<?php

namespace App\Services;

use App\Models\Delivery;
use App\Models\ShopOrder;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class DeliveryService
{
    /**
     * Allowed status transitions graph.
     */
    protected array $allowedTransitions = [
        'pending' => ['assigned', 'out_for_delivery', 'cancelled'],
        'assigned' => ['out_for_delivery', 'pending', 'cancelled'],
        'out_for_delivery' => ['delivered', 'failed', 'cancelled'],
        'delivered' => [], // Terminal state
        'failed' => ['out_for_delivery', 'cancelled'], // Can retry dispatch or cancel
        'cancelled' => [], // Terminal state
    ];

    /**
     * Create a new delivery record linked to a shop order.
     */
    public function createDelivery(ShopOrder $order, array $data, int $userId): Delivery
    {
        return DB::transaction(function () use ($order, $data, $userId) {
            // 1. Duplicate active delivery check for the same order
            $activeDeliveryExists = Delivery::where('shop_order_id', $order->id)
                ->whereIn('status', ['pending', 'assigned', 'out_for_delivery'])
                ->exists();

            if ($activeDeliveryExists) {
                throw new InvalidArgumentException(sprintf(
                    'An active delivery dispatch already exists for order "%s".',
                    $order->order_number
                ));
            }

            // 2. Pre-fill address and contact info from shop if omitted
            $shop = $order->shop;
            $deliveryAddress = $data['delivery_address'] ?? $shop->address ?? 'Main Shop Premises';
            $contactPerson = $data['contact_person'] ?? $shop->owner_name ?? 'Shop Manager';
            $contactPhone = $data['contact_phone'] ?? $shop->phone ?? '';

            // 3. Determine initial status based on staff assignment
            $assignedTo = !empty($data['assigned_to']) ? (int) $data['assigned_to'] : null;
            $initialStatus = $data['status'] ?? ($assignedTo ? 'assigned' : 'pending');

            $deliveryNumber = Delivery::generateDeliveryNumber();

            $delivery = Delivery::create([
                'delivery_number' => $deliveryNumber,
                'shop_order_id' => $order->id,
                'shop_id' => $order->shop_id,
                'delivery_date' => $data['delivery_date'] ?? today()->toDateString(),
                'status' => $initialStatus,
                'delivery_address' => $deliveryAddress,
                'contact_person' => $contactPerson,
                'contact_phone' => $contactPhone,
                'assigned_to' => $assignedTo,
                'created_by' => $userId,
                'notes' => $data['notes'] ?? null,
                'dispatched_at' => $initialStatus === 'out_for_delivery' ? now() : null,
                'delivered_at' => $initialStatus === 'delivered' ? now() : null,
            ]);

            // Sync shop order status if applicable
            $this->syncShopOrderStatus($delivery);

            return $delivery;
        });
    }

    /**
     * Update delivery status adhering to workflow transition rules.
     */
    public function updateDeliveryStatus(Delivery $delivery, string $newStatus, ?int $assignedTo = null): Delivery
    {
        return DB::transaction(function () use ($delivery, $newStatus, $assignedTo) {
            $currentStatus = $delivery->status;

            if ($currentStatus === $newStatus) {
                if ($assignedTo !== null && $delivery->assigned_to !== $assignedTo) {
                    $delivery->assigned_to = $assignedTo;
                    if ($currentStatus === 'pending') {
                        $delivery->status = 'assigned';
                    }
                    $delivery->save();
                }
                return $delivery;
            }

            // Validate status transition
            $allowed = $this->allowedTransitions[$currentStatus] ?? [];
            if (!in_array($newStatus, $allowed)) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid status transition from "%s" to "%s".',
                    ucfirst($currentStatus),
                    ucfirst($newStatus)
                ));
            }

            $delivery->status = $newStatus;

            if ($assignedTo !== null) {
                $delivery->assigned_to = $assignedTo;
            }

            if ($newStatus === 'out_for_delivery' && !$delivery->dispatched_at) {
                $delivery->dispatched_at = now();
            }

            if ($newStatus === 'delivered') {
                $delivery->delivered_at = now();
            }

            $delivery->save();

            // Sync shop order status
            $this->syncShopOrderStatus($delivery);

            return $delivery;
        });
    }

    /**
     * Synchronize the linked ShopOrder status according to Delivery progress.
     */
    protected function syncShopOrderStatus(Delivery $delivery): void
    {
        $order = $delivery->shopOrder;
        if (!$order) {
            return;
        }

        if ($delivery->status === 'out_for_delivery' && $order->status !== 'dispatched') {
            $order->update(['status' => 'dispatched']);
        } elseif ($delivery->status === 'delivered' && $order->status !== 'delivered') {
            $order->update(['status' => 'delivered']);
        }
    }
}
