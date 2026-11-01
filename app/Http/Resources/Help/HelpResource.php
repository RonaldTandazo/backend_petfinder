<?php

namespace App\Http\Resources\Help;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HelpResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'subject'  => $this->subject,
            'comment'  => $this->comment,
            'pictures' => $this->pictures->map(fn ($picture) => [
                'url' => $picture->path
                    ? config('services.pictures.host') . $picture->path
                    : null,
            ])->values()->all()
        ];
    }
}