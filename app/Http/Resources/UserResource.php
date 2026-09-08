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
            'full_name'   => trim($this->first_names) . " " . trim($this->last_names),
            'email'       => $this->email,
            'telephone'   => $this->telephone,
            'country_id'  => $this->country_id,
            'city'        => $this->city,
            'address'     => $this->address,
            'gender_id'   => $this->gender_id,
            'avatar'      => $this->avatar,
        ];
    }
}
