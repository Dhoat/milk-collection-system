<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FarmerResource extends JsonResource
{
    /**
     * Transform the resource into an array for API responses.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'village_id' => $this->village_id,
            'farmer_code' => $this->farmer_code,
            'name' => $this->name,
            'father_name' => $this->father_name,
            'mobile' => $this->mobile,
            'alternate_mobile' => $this->alternate_mobile,
            'address' => $this->address,
            'gender' => $this->gender,
            'joining_date' => $this->joining_date?->format('Y-m-d'),
            'bank_name' => $this->bank_name,
            'account_number' => $this->account_number,
            'ifsc_code' => $this->ifsc_code,
            'status' => (bool) $this->status,
            'village' => new VillageResource($this->whenLoaded('village')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
