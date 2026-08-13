<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MilkStockResource extends JsonResource
{
    /**
     * Transform the resource into an array for API consumption.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transaction_date' => $this->transaction_date?->format('Y-m-d'),
            'type' => $this->type,
            'item_type' => $this->item_type,
            'quantity' => (float) $this->quantity,
            'fat' => $this->fat !== null ? (float) $this->fat : null,
            'snf' => $this->snf !== null ? (float) $this->snf : null,
            'milk_receiving_id' => $this->milk_receiving_id,
            'created_by' => $this->created_by,
            'source_or_reason' => $this->source_or_reason,
            'notes' => $this->notes,
            'milk_receiving' => new MilkReceivingResource($this->whenLoaded('milkReceiving')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
