<?php

namespace App\Http\Resources\LostPet;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LostPetListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'race'              => $this->race,
            'species_id'        => $this->species_id,
            'species'           => $this->whenLoaded('species', fn () => $this->species->name),
            'gender_tag'        => $this->whenLoaded('animalGender', fn () => $this->animalGender->tag),
            'city'              => $this->city,
            'event_address'     => $this->event_address,
            'latitude'          => $this->latitude,
            'longitude'         => $this->longitude,
            'event_date'        => $this->event_date->toIso8601String(),
            'report_status_tag' => $this->whenLoaded('reportStatus', fn () => $this->reportStatus->tag),
            'report_status'     => $this->whenLoaded('reportStatus', fn () => $this->reportStatus->name),
            'picture'           => config('services.pets.pictures.host') . $this->mainPicture->path
        ];
    }
}