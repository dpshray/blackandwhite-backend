<?php

namespace App\Http\Resources\Wishlist;

use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WishlistResource extends JsonResource
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

            // Product Info
            'product_id' => $this->product_id,
            'title' => $this->product->name,

            // Variant Info
            'variant_id' => $this->variant_id,
            'variant' => [
                'size' => $this->variant->size ?? null,
                'color' => $this->variant->color ?? null,
                'price' => $this->variant->price ?? null,
                'discount_price' => $this->variant->discount_price ?? null,
                'stock' => $this->variant->stock ?? null,
                'images' => $this->variant->getMedia(Variant::MEDIA_NAME)->map(function ($media) {
                    return $media->getUrl();
                }),
            ],
        ];
    }
}
