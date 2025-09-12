<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminOrderDetailresource extends JsonResource
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
            'id'           => $this->id,
            'total_amount' => $this->total_amount,
            'status'       => $this->status,
            'created_at'   => $this->created_at->format('Y-m-d H:i:s'),

            'user' => [
                'id'      => $this->user->id,
                'name'    => $this->user->name,
                'email'   => $this->user->email,
                'addresses' => $this->user->addresses->map(function ($address) {
                    return [
                        'first_name' => $address->first_name,
                        'last_name' => $address->last_name,
                        'email' => $address->email,
                        'state' => $address->state,
                        'city' => $address->city,
                        'address' => $address->address,
                        'contact_number' => $address->contact_number,
                    ];
                }),
            ],

            'items' => $this->items->map(function ($item) {
                return [
                    'product_name'  => $item->product->name,
                    'variant_size'  => $item->variant->size ?? null,
                    'variant_color' => $item->variant->color ?? null,
                    'quantity'      => $item->quantity,
                    'price'         => $item->total_amount,
                    'image'         => $item->variant->getFirstMediaUrl('variant'),
                ];
            }),
        ];
    }
}
