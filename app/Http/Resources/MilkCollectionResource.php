<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MilkCollectionResource extends JsonResource
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
            'farmer_id' => $this->farmer_id,
            'collection_date' => $this->collection_date?->format('Y-m-d'),
            'shift' => $this->shift,
            'milk_quantity' => (float) $this->milk_quantity,
            'fat' => $this->fat !== null ? (float) $this->fat : null,
            'snf' => $this->snf !== null ? (float) $this->snf : null,
            'rate' => (float) $this->rate,
            'amount' => (float) $this->amount,
            'notes' => $this->notes,
            'farmer' => new FarmerResource($this->whenLoaded('farmer')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
