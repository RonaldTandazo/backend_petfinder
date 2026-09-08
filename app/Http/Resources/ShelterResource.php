<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShelterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'business_name'      => $this->business_name,
            'tax_identification' => $this->tax_identification,
            'email'              => $this->email,
            'telephone'          => $this->telephone,
            'country_id'         => $this->country_id,
            'city'               => $this->city,
            'physical_address'   => $this->physical_address,
            'latitude'           => $this->latitude,
            'longitude'          => $this->longitude,
            'web_page'           => $this->web_page,
            'business_hours'     => $this->business_hours,
            'avatar'             => $this->avatar,
            'verified'           => $this->verified,
        ];
    }
}