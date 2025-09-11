<?php

namespace App\Http\Resources\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'id'       => $this->id,
            'quantity' => $this->quantity,

            // Product info
            'product' => [
                'id'    => $this->product->id ?? null,
                'title' => $this->product->name ?? null,

                'variant' => $this->when($this->variant, function () {
                    return [
                        'id'             => $this->variant->id,
                        'size'           => $this->variant->size,
                        'color'          => $this->variant->color,
                        'image'          => $this->variant->getFirstMediaUrl('variant', 'image') ?: null,
                        'price'          => $this->variant->price,
                        'discount_price' => $this->variant->discount_price,
                        'quantity'       => $this->variant->stock,
                    ];
                }, null), // if no variant, return null instead of error
            ],
        ];
    }
}
