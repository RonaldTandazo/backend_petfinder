<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SessionInfoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'main_id'  => $this->id,
            'tutor_id' => $this->tutor?->id,
            'name'     => $this->name,
            'email'    => $this->email,
            'avatar'   => $this->avatar ? [
                'id'      => $this->avatar->id,
                'url'     => config('services.pictures.host') . $this->avatar->path,
                'is_main' => $this->avatar->is_main,
            ] : null
        ];
    }
}
