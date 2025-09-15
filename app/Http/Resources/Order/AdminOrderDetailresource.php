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
                'id'    => $this->user->id,
                'name'  => $this->user->name,
                'email' => $this->user->email,
            ],

            // 🔹 Only show the billing info linked to this order:
            'billing_information' => $this->billingInformation ? [
                'first_name'     => $this->billingInformation->first_name,
                'last_name'      => $this->billingInformation->last_name,
                'email'          => $this->billingInformation->email,
                'state'          => $this->billingInformation->state,
                'city'           => $this->billingInformation->city,
                'address'        => $this->billingInformation->address,
                'contact_number' => $this->billingInformation->contact_number,
            ] : null,

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
