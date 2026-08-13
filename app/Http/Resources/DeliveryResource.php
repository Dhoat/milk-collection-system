<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryResource extends JsonResource
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
            'delivery_number' => $this->delivery_number,
            'shop_order_id' => $this->shop_order_id,
            'shop_id' => $this->shop_id,
            'delivery_date' => $this->delivery_date?->format('Y-m-d'),
            'status' => $this->status,
            'delivery_address' => $this->delivery_address,
            'contact_person' => $this->contact_person,
            'contact_phone' => $this->contact_phone,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
            'notes' => $this->notes,
            'dispatched_at' => $this->dispatched_at?->toIso8601String(),
            'delivered_at' => $this->delivered_at?->toIso8601String(),
            'shop_order' => new ShopOrderResource($this->whenLoaded('shopOrder')),
            'shop' => new ShopResource($this->whenLoaded('shop')),
            'assigned_staff' => new UserResource($this->whenLoaded('assignedStaff')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
