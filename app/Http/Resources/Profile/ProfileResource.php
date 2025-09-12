<?php

namespace App\Http\Resources\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            'name'=>$this->name,
            'phone_number'=>$this->profile->phone_number??null,
            'gender'=>$this->profile->gender??null,
            'date_of_birth'=>$this->profile->date_of_birth??null,
            'profile_image'=>$this->profile->getFirstMediaUrl('profile','image') ?? null,
        ];
    }
}
