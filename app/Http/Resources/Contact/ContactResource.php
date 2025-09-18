<?php

namespace App\Http\Resources\Contact;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
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
            'firstname'=>$this->firstname,
            'lastname'=>$this->lastname,
            'email'=>$this->email,
            'phone'=>$this->phone,
            'message'=>$this->message,
        ];
    }
}
