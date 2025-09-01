<?php

namespace App\Http\Resources\Billing_Information;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return[
            'id'=>$this->id,
            'first_name'=>$this->first_name,
            'last_name'=>$this->last_name,
            'email'=>$this->email,
            'state'=>$this->state,
            'city'=>$this->city,
            'address'=>$this->address,
            'contact_number'=>$this->contact_number,
        ];
    }
}
