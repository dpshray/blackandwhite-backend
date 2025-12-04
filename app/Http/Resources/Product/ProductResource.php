<?php

namespace App\Http\Resources\Product;

use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'product_code' => $this->product_code,
            'title' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'discount_price' => $this->discount_price,
            'discount_percent' => $this->discount_percent,
            'pattern' => $this->pattern,
            'fabric' => $this->fabric,
            'material' => $this->material,
            'bestseller'=> (bool) $this->bestseller,
            'limited'=> (bool) $this->limited,
            // 'image' => $this->getFirstMediaUrl('product', 'image') ?: null,
            'size_detail'=>$this->getFirstMediaUrl(Product::SIZE_DETAIL),
            'image' => $this->getMedia(Product::MEDIA_NAME)->map(function ($media) {
                return $media->getUrl();
            }),
            'main_image'=>$this->getFirstMediaUrl(Product::MAIN_IMAGE),
            'categories' => $this->categories->map(function ($category) {
                return [
                    'categories_id' => $category->id,
                    'categories_title' => $category->title,
                    'categories_slug' => $category->slug,
                ];
            }),
            'variants' => $this->variants->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'size' => $variant->size,
                    'color' => $variant->color,
                    'stock' => $variant->stock,
                ];
            }),
        ];
    }
}
