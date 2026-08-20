<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'first_names' => $this->first_names,
            'last_names'  => $this->last_names,
            'full_name'   => trim("{$this->first_names} {$this->last_names}"),
            'username'    => $this->username,
            'email'       => $this->email,
            'telephone'   => $this->telephone,
            'city'        => $this->city,
            'address'     => $this->address,
            'avatar'      => $this->avatar,
            'country'     => $this->whenLoaded('country', function () {
                return [
                    'id'           => $this->country->id,
                    'name'         => $this->country->name,
                    'abbreviation' => $this->country->abbreviation,
                ];
            }),
            'gender'      => $this->whenLoaded('gender', function () {
                return [
                    'id'   => $this->gender->id,
                    'name' => $this->gender->name,
                    'tag'  => $this->gender->tag,
                ];
            }),
            'tutor_id'     => $this->whenLoaded('tutor', function () {
                return $this->tutor->id;
            }),
            'created_at'  => $this->created_at?->toIso8601String(),
        ];
    }
}
