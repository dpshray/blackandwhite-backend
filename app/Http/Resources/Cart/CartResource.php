<?php

namespace App\Http\Resources\Cart;

use App\Models\Product;
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
                'price' => $this->product->price,
                'discount_price' => $this->product->discount_price,
                'image'=> $this->product->getFirstMediaUrl(Product::MAIN_IMAGE) ?: null,
                // 'main_image'=>$this->product->getFirstMediaUrl(Product::MAIN_IMAGE),
                'variant' => $this->when($this->variant, function () {
                    return [
                        'id'=> $this->variant->id,
                        'size'=> $this->variant->size,
                        'color'=> $this->variant->color,
                        'quantity'=>$this->variant->stock,
                    ];
                }, null), // if no variant, return null instead of error
            ],
        ];
    }
}
