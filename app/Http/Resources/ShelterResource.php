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
            'physical_address'   => $this->physical_address,
            'city'               => $this->city,
            'latitude'           => $this->latitude,
            'longitude'          => $this->longitude,
            'web_page'           => $this->web_page,
            'business_hours'     => $this->business_hours,
            'logo'               => $this->logo,
            'verified'           => (bool) ($this->verified ?? false),
            'country'            => $this->whenLoaded('country', function () {
                return [
                    'id'           => $this->country->id,
                    'name'         => $this->country->name,
                    'abbreviation' => $this->country->abbreviation,
                ];
            }),
            'tutor_id'           => $this->whenLoaded('tutor', function () {
                return $this->tutor->id;
            }),
            'created_at'         => $this->created_at?->toIso8601String(),
        ];
    }
}