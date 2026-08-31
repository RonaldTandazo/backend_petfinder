<?php

namespace App\Services\Adoption;

use App\Models\Pet;

class AdoptionService
{

    public function getAdoptionPets(int $page, int $limit): array
    {
        $skip = ($page - 1) * $limit;

        $pets = Pet::where('pet_status_id', 1)
            ->select(['id', 'name', 'species_id', 'animal_gender_id', 'race', 'born_date', 'is_urgent'])
            ->with(['animalGender', 'species'])
            ->orderByDesc('is_urgent')
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
