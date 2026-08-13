<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopOrderResource extends JsonResource
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
            'order_number' => $this->order_number,
            'shop_id' => $this->shop_id,
            'order_date' => $this->order_date?->format('Y-m-d'),
            'status' => $this->status,
            'subtotal' => (float) $this->subtotal,
            'discount' => (float) $this->discount,
            'total_amount' => (float) $this->total_amount,
            'stock_deducted' => (bool) $this->stock_deducted,
            'created_by' => $this->created_by,
            'notes' => $this->notes,
            'shop' => new ShopResource($this->whenLoaded('shop')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'items' => ShopOrderItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
