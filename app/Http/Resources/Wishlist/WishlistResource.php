<?php

namespace App\Http\Resources\Wishlist;

use App\Models\Product;
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
            'slug' => $this->product->slug,
            'images' => $this->product->getMedia(Product::MEDIA_NAME)->map(function ($media) {
                return $media->getUrl();
            }),
            //category
            'categories' => $this->product->categories->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'slug' => $cat->slug,
                    'name' => $cat->title,
                ];
            }),
            // Variant Info
            'variant_id' => $this->variant_id,
            'variant' => [
                'size' => $this->variant->size ?? null,
                'color' => $this->variant->color ?? null,
                'price' => $this->variant->price ?? null,
                'discount_price' => $this->variant->discount_price ?? null,
                'discount_percent' => $this->variant->discount_percent,
                'stock' => $this->variant->stock ?? null,
            ],
        ];
    }
}
