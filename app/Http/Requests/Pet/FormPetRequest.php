<?php

namespace App\Http\Requests\Pet;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class FormPetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->tutor !== null;
    }

    public function rules(): array
    {
        return [
            'name'                => ['required', 'string', 'max:100'],
            'species_id'          => ['required', 'integer', 'exists:species,id'],
            'race'                => ['nullable', 'string', 'max:50'],
            'color'               => ['nullable', 'string', 'max:50'],
            'born_date'           => ['required', 'date', 'before_or_equal:today'],
            'animal_gender_id'    => ['required', 'integer', 'exists:animal_genders,id'],
            'size_id'             => ['required', 'integer', 'exists:sizes,id'],
            'description'         => ['nullable', 'string', 'max:500'],
            'pet_status_id'       => ['nullable', 'integer', 'exists:pet_statuses,id'],
            'health_conditions'   => ['nullable', 'array'],
            'health_conditions.*' => ['integer', 'exists:health_conditions,id'],
            'photos'              => [
                'nullable',
                'array',
                'max:5',
                function ($attribute, $value, $fail) {
                    if (!empty($value)) {
                        $mainCount = collect($value)->filter(function ($photo) {
                            return isset($photo['is_main']) && filter_var($photo['is_main'], FILTER_VALIDATE_BOOLEAN);
                        })->count();

                        if ($mainCount === 0) {
                            $fail('Debe seleccionar exactamente una foto como principal');
                        } elseif ($mainCount > 1) {
                            $fail('Solo una foto puede ser la principal');
                        }
                    }
                },
            ],
            'photos.*.path_temp'  => ['required', 'string', 'max:255'],
            'photos.*.is_main'    => ['required', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'pet_status_id' => $this->input('pet_status_id', 1)
        ]);
    }

    public function messages(): array
    {
        return [
            'name.required'               => 'El nombre de la mascota es obligatorio',
            'species_id.required'         => 'Debe seleccionar una especie',
            'species_id.exists'           => 'La especie seleccionada no es válida',
            'animal_gender_id.required'   => 'Debe seleccionar un género',
            'animal_gender_id.exists'     => 'El género seleccionado no es válido',
            'size_id.required'            => 'Debe seleccionar un tamaño',
            'size_id.exists'              => 'El tamaño seleccionado no es válido',
            'pet_status_id.exists'        => 'El estado de adopción seleccionado no es válido',
            'born_date.required'          => 'La fecha de nacimiento es requerida',
            'born_date.before_or_equal'   => 'La fecha de nacimiento no puede ser futura',
            'health_conditions.array'     => 'El formato de las condiciones de salud debe ser una lista',
            'health_conditions.*.integer' => 'Cada condición de salud debe ser un identificador válido',
            'health_conditions.*.exists'  => 'Una o más condiciones de salud seleccionadas no existen',
            'photos.array'                => 'El formato de las fotos es inválido',
            'photos.max'                  => 'No puede adjuntar más de 5 fotos',
            'photos.*.path_temp.required' => 'El archivo temporal de la imagen es obligatorio',
            'photos.*.path_temp.string'   => 'El archivo temporal de la imagen debe ser una cadena válida',
            'photos.*.is_main.required'   => 'Debe indicar cuál foto es la principal',
            'photos.*.is_main.boolean'    => 'El campo principal debe ser verdadero o falso',
        ];
    }
}