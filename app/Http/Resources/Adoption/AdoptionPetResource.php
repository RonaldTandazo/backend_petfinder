<?php

namespace App\Http\Resources\Adoption;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdoptionPetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'species_id'       => $this->species_id,
            'species'          => $this->whenLoaded('species', fn () => $this->species->name),
            'gender_id'        => $this->animal_gender_id,
            'gender_tag'       => $this->whenLoaded('animalGender', fn () => $this->animalGender->tag),
            'gender'           => $this->whenLoaded('animalGender', fn () => $this->animalGender->name),
            'size_id'          => $this->size_id,
            'size'             => $this->whenLoaded('size', fn () => $this->size->name),
            'born_date'        => $this->born_date->toIso8601String(),
            'race'             => $this->race,
            'color'            => $this->color,
            'city'             => $this->city,
            'address'          => $this->address,
            'latitude'         => $this->latitude,
            'longitude'        => $this->longitude,
            'phone_home'       => $this->phone_home,
            'phone_mobile'     => $this->phone_mobile,
            'is_urgent'        => $this->is_urgent,
            'description'      => $this->description,
            'pet_status_tag'   => $this->whenLoaded('petStatus', fn () => $this->petStatus->tag),
            'pet_status'       => $this->whenLoaded('petStatus', fn () => $this->petStatus->name),            
            'age'              => $this->age['label'],
            'pictures'         => $this->pictures->map(fn ($picture) => config('services.pets.pictures.host') . $picture->path)
        ];
    }
}