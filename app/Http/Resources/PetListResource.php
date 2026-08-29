<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PetListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'species'   => $this->whenLoaded('species', fn () => $this->species->name),
            'gender'    => $this->whenLoaded('animalGender', fn () => $this->animalGender->name),
            'size'      => $this->whenLoaded('size', fn () => $this->size->name),
            'picture'   => $this->whenLoaded('mainPicture', fn () => $this->mainPicture?->path ?? $this->mainPicture?->path_temp),
            'born_date' => $this->born_date,
            'age'       => $this->age,
        ];
    }
}