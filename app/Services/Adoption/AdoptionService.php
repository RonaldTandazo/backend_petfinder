<?php

namespace App\Services\Adoption;

use App\Models\Pet;
use Illuminate\Database\Eloquent\Collection;

class AdoptionService
{

    public function getAdoptionPets(int $page, int $limit): array
    {
        $skip = ($page - 1) * $limit;

        $pets = Pet::where('pet_status_id', 1)
            ->select(['id', 'name', 'species_id', 'animal_gender_id', 'race', 'born_date'])
            ->with(['animalGender', 'species'])
            ->latest()
            ->skip($skip)
            ->take($limit + 1)
            ->get();

        $hasMore = $pets->count() > $limit;

        return [
            'items'   => $pets->take($limit),
            'hasMore' => $hasMore,
        ];
    }
}
