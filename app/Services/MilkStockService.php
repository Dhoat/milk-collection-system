<?php

namespace App\Services;

use App\Models\MilkReceiving;
use App\Models\MilkStock;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class MilkStockService
{
    /**
     * Synchronize a Milk Receiving record into the Stock IN ledger.
     * Idempotent & prevents duplicate stock entries for the same receiving record.
     */
    public function syncFromReceiving(MilkReceiving $receiving): MilkStock
    {
        return DB::transaction(function () use ($receiving) {
            $receiving->loadMissing('village');

            $sourceReason = sprintf(
                'Milk Receiving - %s (%s - %s)',
                $receiving->village->name ?? 'Unknown Village',
                $receiving->receiving_date->format('M d, Y'),
                ucfirst($receiving->shift)
            );

            return MilkStock::updateOrCreate(
                ['milk_receiving_id' => $receiving->id],
                [
                    'transaction_date' => $receiving->receiving_date,
                    'type' => 'in',
                    'item_type' => 'raw_milk',
                    'quantity' => $receiving->received_quantity,
                    'fat' => $receiving->received_fat,
                    'snf' => $receiving->received_snf,
                    'created_by' => $receiving->verified_by,
                    'source_or_reason' => $sourceReason,
                    'notes' => $receiving->notes,
                ]
            );
        });
    }

    /**
     * Safely remove the Stock IN entry when a Milk Receiving record is cancelled/deleted.
     */
    public function removeFromReceiving(MilkReceiving $receiving): bool
    {
        return DB::transaction(function () use ($receiving) {
            return (bool) MilkStock::where('milk_receiving_id', $receiving->id)->delete();
        });
    }

    /**
     * Record a manual Stock OUT transaction.
     * Throws InvalidArgumentException if requested quantity exceeds available stock.
     */
    public function recordStockOut(array $data, int $userId): MilkStock
    {
        return DB::transaction(function () use ($data, $userId) {
            $quantity = (float) $data['quantity'];
            $availableStock = MilkStock::getAvailableStock();

            if ($quantity > $availableStock) {
                throw new InvalidArgumentException(sprintf(
                    'Stock OUT quantity (%.2f L) cannot exceed available milk stock (%.2f L).',
                    $quantity,
                    $availableStock
                ));
            }

            return MilkStock::create([
                'transaction_date' => $data['transaction_date'] ?? today()->toDateString(),
                'type' => 'out',
                'item_type' => 'raw_milk',
                'quantity' => $quantity,
                'fat' => $data['fat'] ?? null,
                'snf' => $data['snf'] ?? null,
                'created_by' => $userId,
                'source_or_reason' => $data['source_or_reason'],
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }
}
