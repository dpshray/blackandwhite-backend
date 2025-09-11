<?php

namespace App\Http\Resources\ProductStock;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class StockAlertsCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
