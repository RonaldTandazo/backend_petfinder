<?php

namespace App\Services\Catalog;

use App\Models\Catalog\AnimalGender;
use App\Models\Catalog\HealthCondition;
use App\Models\Catalog\Size;
use App\Models\Catalog\Species;

class CatalogService
{
    public function getPublishPetCatalog(): array
    {
        return [
            'species'           => Species::select('id', 'name', 'tag')->get(),
            'genders'           => AnimalGender::select('id', 'name', 'tag')->get(),
            'sizes'             => Size::select('id', 'name', 'tag')->get(),
            'health_conditions' => HealthCondition::select('id', 'name', 'tag')->get(),
        ];
    }
}
