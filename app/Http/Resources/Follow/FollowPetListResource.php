<?php

namespace App\Http\Resources\Follow;

use App\Models\LostPet\LostPet;
use App\Models\Pet\Pet;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FollowPetListResource extends JsonResource
{
    private const PET_TYPES_MODEL = [
        Pet::class     => 'adoption',
        LostPet::class => 'lost_pet'
    ];
    
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'pet_id'     => $this->pet->id,
            'name'       => $this->pet->name,
            'type'       => self::PET_TYPES_MODEL[$this->pet_type],
            'latitude'   => $this->pet->latitude,
            'longitude'  => $this->pet->longitude,
            'picture'    => $this->pet->mainPicture ? config('services.pictures.host') . $this->pet->mainPicture->path : null
        ];
    }
}