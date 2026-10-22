<?php

namespace App\Http\Resources\Legal;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LegalDocumentResource  extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this['id'],
            'name'         => $this['name'],
            'tag'          => $this['tag'],
            'version'      => $this['version'],
            'path'         => $this['path'],
            'published_at' => $this['published_at'],
        ];
    }
}
