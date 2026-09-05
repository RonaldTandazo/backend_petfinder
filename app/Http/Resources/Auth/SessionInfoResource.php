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
            'tutor_id' => $this->whenLoaded('tutor', fn () => $this->tutor->id),
        ];
    }
}
