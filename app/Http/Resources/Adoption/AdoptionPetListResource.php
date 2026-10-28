<?php

namespace App\Http\Resources\Adoption;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdoptionPetListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'species_id' => $this->species_id,
            'species'    => $this->whenLoaded('species', fn () => $this->species->name),
            'race'       => $this->race,
            'gender_tag' => $this->whenLoaded('animalGender', fn () => $this->animalGender->tag),
            'size'       => $this->whenLoaded('size', fn () => $this->size->name),
            'age'        => $this->age['label'],
            'city'       => $this->city,
            'latitude'   => $this->latitude,
            'longitude'  => $this->longitude,
            'is_urgent'  => $this->is_urgent,
            'picture'    => $this->mainPicture ? config('services.pictures.host') . $this->mainPicture->path : null
        ];
    }
}