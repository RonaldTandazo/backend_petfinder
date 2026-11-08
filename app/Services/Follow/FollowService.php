<?php

namespace App\Services\Follow;

use App\Exceptions\CustomValidationException;
use App\Models\Follow\PetFollows;
use App\Models\LostPet\LostPet;
use App\Models\Pet\Pet;

class FollowService
{
    private const PET_TYPES_MODEL = [
        'adoption' => Pet::class,
        'lost_pet' => LostPet::class,
    ];

    private const PET_TYPES_LABEL = [
        'adoption' => 'Adopciones',
        'lost_pet' => 'Perdidos',
    ];

    private function resolvePetModel(string $petType): string
    {
        return self::PET_TYPES_MODEL[$petType] ?? throw new CustomValidationException('Categoría no válida');
    }

    public function setPetFollow(int $petId, int $tutorId, bool $state, string $petType): void
    {
        $model = $this->resolvePetModel($petType);

        $pet = $model::findOrFail($petId);

        if ($state) {
            PetFollows::firstOrCreate([
                'pet_id'   => $pet->id,
                'pet_type' => $model,
                'tutor_id' => $tutorId,
            ]);
        } else {
            PetFollows::where('pet_id', $petId)
                ->where('pet_type', $model)
                ->where('tutor_id', $tutorId)
                ->delete();
        }
    }

    public function getPetFollow(int $petId, int $tutorId, string $petType): bool
    {
        $model = $this->resolvePetModel($petType);

        return PetFollows::where('pet_id', $petId)
            ->where('tutor_id', $tutorId)
            ->where('pet_type', $model)
            ->exists();
    }

    public function getPetFollowsCount(int $tutorId): array
    {
        $counters = [];
        $total = 0;

        foreach (self::PET_TYPES_MODEL as $petType => $class) {
            $count = PetFollows::where('tutor_id', $tutorId)
                ->where('pet_type', $class)
                ->count();

            $counters[] = [
                'label'  => self::PET_TYPES_LABEL[$petType],
                'tag'    => strtoupper($petType),
                'count'  => $count,
                'action' => 'filter'
            ];

            $total += $count;
        }

        array_unshift($counters, [
            'label'  => 'Todos',
            'tag'    => 'ALL',
            'count'  => $total,
            'action' => 'filter'
        ]);
        
        return $counters;
    }

    public function getPetFollows(int $page, int $limit, int $tutorId, string $category): array
    {
        $skip = ($page - 1) * $limit;

        $query = PetFollows::query()
            ->where('tutor_id', $tutorId);

        if ($category !== 'all') {
            $query->where('pet_type', $this->resolvePetModel($category));
        } elseif (!in_array($category, ['all', ...array_keys(self::PET_TYPES_MODEL)], true)) {
            throw new CustomValidationException('Categoría no válida');
        }

        $follows = $query
            ->with('pet')
            ->orderByDesc('created_at')
            ->skip($skip)
            ->take($limit + 1)
            ->get();

        return [
            'items'   => $follows->take($limit)->values(),
            'hasMore' => $follows->count() > $limit,
        ];
    }
}