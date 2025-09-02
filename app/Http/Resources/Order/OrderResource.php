<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'total_amount' => $this->total_amount,
            'status' => $this->status,
            'items' => $this->items->map(function ($item) {
                return [
                    'product_name' => $item->product->name ?? null,
                    'variant_size' => $item->variant->size ?? null,
                    'variant_color' => $item->variant->color ?? null,
                    'quantity' => $item->quantity,
                    'price' => $item->variant->discount_price ?? $item->variant->price,
                    'image' => $item->product?->getFirstMediaUrl('product') ?? null,
                ];
            }),
        ];
    }
}
