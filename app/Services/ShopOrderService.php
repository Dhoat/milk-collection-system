<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ShopOrderService
{
    /**
     * Create a new shop order with items and calculate server-side totals.
     */
    public function createOrder(array $data, array $items, int $userId): ShopOrder
    {
        return DB::transaction(function () use ($data, $items, $userId) {
            $orderNumber = ShopOrder::generateOrderNumber();
            $subtotal = 0.00;
            $preparedItems = [];

            foreach ($items as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                $quantity = (float) $itemData['quantity'];
                $unitPrice = (float) ($itemData['unit_price'] ?? $product->unit_price);
                $lineTotal = round($quantity * $unitPrice, 2);
                $subtotal += $lineTotal;

                $preparedItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit' => $product->unit,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ];
            }

            $discount = (float) ($data['discount'] ?? 0.00);
            $totalAmount = max(0, round($subtotal - $discount, 2));
            $initialStatus = $data['status'] ?? 'pending';

            $order = ShopOrder::create([
                'order_number' => $orderNumber,
                'shop_id' => $data['shop_id'],
                'order_date' => $data['order_date'] ?? today()->toDateString(),
                'status' => 'pending', // Create as pending first
                'subtotal' => round($subtotal, 2),
                'discount' => $discount,
                'total_amount' => $totalAmount,
                'stock_deducted' => false,
                'created_by' => $userId,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($preparedItems as $item) {
                $order->items()->create($item);
            }

            // If initial status requested is confirmed or beyond, validate stock and transition
            if ($initialStatus !== 'pending' && $initialStatus !== 'cancelled') {
                $this->updateOrderStatus($order, $initialStatus);
            }

            return $order;
        });
    }

    /**
     * Update an order's status and handle stock deduction / reversal idempotently.
     */
    public function updateOrderStatus(ShopOrder $order, string $newStatus): ShopOrder
    {
        return DB::transaction(function () use ($order, $newStatus) {
            $currentStatus = $order->status;

            if ($currentStatus === $newStatus) {
                return $order;
            }

            $shouldDeductStock = in_array($newStatus, ['confirmed', 'preparing', 'dispatched', 'delivered']);

            if ($shouldDeductStock && !$order->stock_deducted) {
                // Validate product stock availability before confirming
                $this->validateStockAvailability($order);

                // Perform stock deduction
                $this->deductOrderStock($order);
                $order->stock_deducted = true;
            } elseif ($newStatus === 'cancelled' && $order->stock_deducted) {
                // Reverse stock deduction on cancellation
                $this->reverseOrderStock($order);
                $order->stock_deducted = false;
            }

            $order->status = $newStatus;
            $order->save();

            return $order;
        });
    }

    /**
     * Validate that all products in the order have sufficient stock available.
     */
    public function validateStockAvailability(ShopOrder $order): void
    {
        $order->loadMissing('items.product');

        foreach ($order->items as $item) {
            $product = $item->product;
            $available = $product->available_stock;

            if ($item->quantity > $available) {
                throw new InvalidArgumentException(sprintf(
                    'Insufficient stock for product "%s". Available: %.2f %s, Requested: %.2f %s.',
                    $product->name,
                    $available,
                    $product->unit,
                    $item->quantity,
                    $product->unit,
                ));
            }
        }
    }

    /**
     * Deduct stock for all items in the order.
     */
    protected function deductOrderStock(ShopOrder $order): void
    {
        $stockService = app(MilkStockService::class);

        foreach ($order->items as $item) {
            $product = $item->product;

            if ($product->category === 'raw_milk') {
                // Deduct from MilkStock raw milk inventory ledger
                $stockService->recordStockOut([
                    'transaction_date' => $order->order_date->toDateString(),
                    'quantity' => $item->quantity,
                    'source_or_reason' => sprintf('Shop Order %s (%s)', $order->order_number, $order->shop->name ?? 'Shop'),
                    'notes' => sprintf('Order #%s dispatch', $order->order_number),
                ], $order->created_by);
            } else {
                // Deduct from Product stock_quantity
                $product->decrement('stock_quantity', $item->quantity);
            }
        }
    }

    /**
     * Reverse stock deduction when an order is cancelled.
     */
    protected function reverseOrderStock(ShopOrder $order): void
    {
        foreach ($order->items as $item) {
            $product = $item->product;

            if ($product->category === 'raw_milk') {
                // Return raw milk to MilkStock inventory
                \App\Models\MilkStock::create([
                    'transaction_date' => today()->toDateString(),
                    'type' => 'in',
                    'item_type' => 'raw_milk',
                    'quantity' => $item->quantity,
                    'created_by' => auth()->id() ?? $order->created_by,
                    'source_or_reason' => sprintf('Cancelled Shop Order %s Reversal', $order->order_number),
                    'notes' => sprintf('Stock returned for cancelled order %s', $order->order_number),
                ]);
            } else {
                // Return quantity to product stock
                $product->increment('stock_quantity', $item->quantity);
            }
        }
    }
}
