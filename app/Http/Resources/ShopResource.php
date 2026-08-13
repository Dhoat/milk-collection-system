<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
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
            'shop_code' => $this->shop_code,
            'name' => $this->name,
            'owner_name' => $this->owner_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'village_id' => $this->village_id,
            'area' => $this->area,
            'address' => $this->address,
            'status' => (bool) $this->status,
            'credit_limit' => (float) $this->credit_limit,
            'notes' => $this->notes,
            'village' => new VillageResource($this->whenLoaded('village')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
