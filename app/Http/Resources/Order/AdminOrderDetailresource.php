<?php

namespace App\Http\Resources\Order;

use App\Models\Product;
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
            'address_details' => [
                'first_name'     => $this->address_details['first_name'] ?? null,
                'last_name'      => $this->address_details['last_name'] ?? null,
                'email'          => $this->address_details['email'] ?? null,
                'state'          => $this->address_details['state'] ?? null,
                'city'           => $this->address_details['city'] ?? null,
                'address'        => $this->address_details['address'] ?? null,
                'contact_number' => $this->address_details['contact_number'] ?? null,
            ],
            'payment_status' => $this->payment_status,
            'items' => $this->items->map(function ($item) {
                return [
                    'product_name'  => $item->product_name,
                    'product_id'    => $item->product_id,
                    'variant_id'   => $item->variant_id,
                    'main_image'    => $item->product?->getFirstMediaUrl(Product::MAIN_IMAGE),
                    'variant_size'  => $item->size ?? null,
                    'variant_color' => $item?->color ?? null,
                    'quantity'      => $item->quantity,
                    'price'         => $item->total_amount,
                ];
            }),
        ];
    }
}
