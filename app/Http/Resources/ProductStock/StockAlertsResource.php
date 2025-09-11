<?php

namespace App\Http\Resources\ProductStock;

use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockAlertsResource extends JsonResource
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
            'title' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'discount_price' => $this->discount_price,
            'discount_percent' => $this->discount_percent,
            'pattern' => $this->pattern,
            'fabric' => $this->fabric,
            'material' => $this->material,
            'image' => $this->getFirstMediaUrl('product', 'image') ?: null,
            'variants' => $this->variants->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'size' => $variant->size,
                    'color' => $variant->color,
                    'price' => $variant->price,
                    'discount_price' => $variant->discount_price,
                    'discount_percent' => $variant->discount_percent,
                    'stock' => $variant->stock,
                    'images' => $variant->getMedia(Variant::MEDIA_NAME)->map(function ($media) {
                        return $media->getUrl();
                    }),
                ];
            }),
        ];
    }
}
