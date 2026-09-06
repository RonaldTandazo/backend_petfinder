<?php

namespace Database\Seeders;

use App\Models\Shelter;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'test@test.com'],
            [
                'first_names' => 'Test',
                'last_names'  => 'User',
                'password'    => '12345678',
            ]
        );

        Tutor::updateOrCreate(
            ['user_id' => $user->id],
            ['tutor_type_id' => 1]
        );

        $shelter = Shelter::updateOrCreate(
            ['email' => 'test@test.com'],
            [
                'name'        => 'Refugio de prueba',
                'password'    => '12345678',
            ]
        );

        Tutor::updateOrCreate(
            ['shelter_id' => $shelter->id],
            ['tutor_type_id' => 1]
        );
    }
}
