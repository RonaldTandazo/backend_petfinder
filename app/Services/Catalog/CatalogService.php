<?php

namespace App\Services\Catalog;

use App\Models\Catalog\AnimalGender;
use App\Models\Catalog\Country;
use App\Models\Catalog\Gender;
use App\Models\Catalog\HealthCondition;
use App\Models\Catalog\Size;
use App\Models\Catalog\Species;

class CatalogService
{
    public function getPetCatalogs(): array
    {
        return [
            'species'           => Species::get(),
            'genders'           => AnimalGender::get(),
            'sizes'             => Size::get(),
            'health_conditions' => HealthCondition::get(),
        ];
    }

    public function getAccountCatalogs(): array
    {
        return [
            'countries' => Country::get(),
            'genders'   => Gender::get(),
        ];
    }
}
