<?php

namespace App\Http\Resources\LostPet;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LostPetSightingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'tutor_id'      => $this->tutor_id,
            'event_date'    => $this->event_date->toIso8601String(),
            'event_address' => $this->event_address,
            'latitude'      => $this->latitude,
            'longitude'     => $this->longitude,
            'comment'       => $this->comment,
            'pictures'      => $this->pictures->map(fn ($picture) => config('services.pets.pictures.host') . $picture->path),
        ];
    }
}