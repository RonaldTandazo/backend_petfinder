<?php

namespace App\Http\Resources\LostPet;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LostPetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'race'              => $this->race,
            'color'             => $this->color,
            'description'       => $this->description,
            'phone_home'        => $this->phone_home,
            'phone_mobile'      => $this->phone_mobile,
            'report_type_tag'   => $this->whenLoaded('reportType', fn () => $this->reportType->tag),
            'report_type'       => $this->whenLoaded('reportType', fn () => $this->reportType->name),
            'species_tag'       => $this->whenLoaded('species', fn () => $this->species->tag),
            'species'           => $this->whenLoaded('species', fn () => $this->species->name),
            'gender_tag'        => $this->whenLoaded('animalGender', fn () => $this->animalGender->tag),
            'gender'            => $this->whenLoaded('animalGender', fn () => $this->animalGender->name),
            'size_tag'          => $this->whenLoaded('size', fn () => $this->size->tag),
            'size'              => $this->whenLoaded('size', fn () => $this->size->name),
            'has_reward'        => $this->has_reward,
            'reward_amount'     => $this->reward_amount,
            'city'              => $this->city,
            'event_address'     => $this->event_address,
            'latitude'          => $this->latitude,
            'longitude'         => $this->longitude,
            'event_date'        => $this->event_date->toIso8601String(),
            'report_status_tag' => $this->whenLoaded('reportStatus', fn () => $this->reportStatus->tag),
            'report_status'     => $this->whenLoaded('reportStatus', fn () => $this->reportStatus->name),
            'closing_date'      => $this->closing_date?->toIso8601String(),
            'pictures'          => $this->pictures->map(fn ($picture) => config('services.pets.pictures.host') . $picture->path),
        ];
    }
}