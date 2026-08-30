<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Genders (Usuarios)
        DB::table('genders')->insertOrIgnore([
            ['id' => 1, 'name' => 'Masculino', 'tag' => 'MALE', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Femenino', 'tag' => 'FEMALE', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Tutor Types
        DB::table('tutor_types')->insertOrIgnore([
            ['id' => 1, 'name' => 'Persona Particular', 'tag' => 'USER', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Refugio / Fundación', 'tag' => 'SHELTER', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Species
        DB::table('species')->insertOrIgnore([
            ['id' => 1, 'name' => 'Perro', 'tag' => 'DOG', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Gato', 'tag' => 'CAT', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Conejo', 'tag' => 'RABBIT', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Ave', 'tag' => 'BIRD', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'name' => 'Otro', 'tag' => 'OTHER', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Animal Genders
        DB::table('animal_genders')->insertOrIgnore([
            ['id' => 1, 'name' => 'Macho', 'tag' => 'MALE', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Hembra', 'tag' => 'FEMALE', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Sizes
        DB::table('sizes')->insertOrIgnore([
            ['id' => 1, 'name' => 'Pequeño', 'tag' => 'SMALL', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Mediano', 'tag' => 'MEDIUM', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Grande', 'tag' => 'LARGE', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Pet Statuses
        DB::table('pet_statuses')->insertOrIgnore([
            ['id' => 1, 'name' => 'Disponible para Adopción', 'tag' => 'AVAILABLE', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'En Proceso de Adopción', 'tag' => 'IN_PROCESS', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Adoptado', 'tag' => 'ADOPTED', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Extraviado', 'tag' => 'LOST', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Report Types
        DB::table('report_types')->insertOrIgnore([
            ['id' => 1, 'name' => 'Mascota Perdida', 'tag' => 'LOST', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Mascota Encontrada', 'tag' => 'FOUND', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Report Statuses
        DB::table('report_statuses')->insertOrIgnore([
            ['id' => 1, 'name' => 'Activo / En Búsqueda', 'tag' => 'ACTIVE', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Resuelto / Retornado', 'tag' => 'RESOLVED', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Cerrado', 'tag' => 'CLOSED', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Lost Pet Event Types (Timeline)
        DB::table('lost_pet_event_types')->insertOrIgnore([
            ['id' => 1, 'name' => 'Avistamiento de Tercero', 'tag' => 'SIGHTING', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Actualización del Tutor', 'tag' => 'TUTOR_UPDATE', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Búsqueda Organizada', 'tag' => 'SEARCH_PARTY', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Resguardado Temporalmente', 'tag' => 'TEMPORARY_SHELTER', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Adoption Statuses
        DB::table('adoption_statuses')->insertOrIgnore([
            ['id' => 1, 'name' => 'Pendiente', 'tag' => 'PENDING', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'En Evaluación', 'tag' => 'IN_REVIEW', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Aprobada', 'tag' => 'APPROVED', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Rechazada', 'tag' => 'REJECTED', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // News Types
        DB::table('news_types')->insertOrIgnore([
            ['id' => 1, 'name' => 'Noticia General', 'tag' => 'GENERAL', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Evento de Adopción', 'tag' => 'EVENT', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Campaña / Donación', 'tag' => 'CAMPAIGN', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Health Condition
        DB::table('health_conditions')->insertOrIgnore([
            ['id' => 1, 'name' => 'Vacunado', 'tag' => 'VACCINATED', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Desparasitado', 'tag' => 'DEWORMED', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Esterilizado / Castrado', 'tag' => 'NEUTERED', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}