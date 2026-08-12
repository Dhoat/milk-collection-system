<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MilkReceivingResource extends JsonResource
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
            'village_id' => $this->village_id,
            'receiving_date' => $this->receiving_date?->format('Y-m-d'),
            'shift' => $this->shift,
            'expected_quantity' => (float) $this->expected_quantity,
            'received_quantity' => (float) $this->received_quantity,
            'quantity_variance' => (float) $this->quantity_variance,
            'quantity_variance_percent' => (float) round($this->quantity_variance_percent, 2),
            'expected_fat' => $this->expected_fat !== null ? (float) round($this->expected_fat, 2) : null,
            'received_fat' => $this->received_fat !== null ? (float) $this->received_fat : null,
            'expected_snf' => $this->expected_snf !== null ? (float) round($this->expected_snf, 2) : null,
            'received_snf' => $this->received_snf !== null ? (float) $this->received_snf : null,
            'status' => $this->status,
            'verified_by' => $this->verified_by,
            'notes' => $this->notes,
            'village' => new VillageResource($this->whenLoaded('village')),
            'verifier' => new UserResource($this->whenLoaded('verifier')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
