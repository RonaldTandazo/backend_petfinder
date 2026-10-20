<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'first_names'  => $this->first_names,
            'last_names'   => $this->last_names,
            'birth_date'   => $this->birth_date,
            'email'        => $this->email,
            'phone_mobile' => $this->phone_mobile,
            'country_id'   => $this->country_id,
            'city'         => $this->city,
            'address'      => $this->address,
            'gender_id'    => $this->gender_id,
            'avatar'       => $this->avatar ? [
                'id'      => $this->avatar->id,
                'url'     => config('services.pictures.host') . $this->avatar->path,
                'is_main' => $this->avatar->is_main,
            ] : null
        ];
    }
}
