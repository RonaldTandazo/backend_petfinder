<?php

namespace App\Http\Requests\LostPet;

use Illuminate\Foundation\Http\FormRequest;

class FormLostPetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->tutor !== null;
    }

    public function rules(): array
    {
        return [
            'name'             => ['required', 'string', 'max:100'],
            'race'             => ['nullable', 'string', 'max:50'],
            'color'            => ['nullable', 'string', 'max:50'],
            'description'      => ['nullable', 'string', 'max:500'],
            'phone_home'       => ['nullable', 'string', 'max:10', 'regex:/^[0-9]{7,10}$/'],
            'phone_mobile'     => ['nullable', 'string', 'max:15', 'regex:/^\+?[0-9]* ?[0-9]*$/'],
            'species_id'       => ['required', 'integer', 'exists:species,id'],
            'animal_gender_id' => ['required', 'integer', 'exists:animal_genders,id'],
            'size_id'          => ['required', 'integer', 'exists:sizes,id'],
            'has_reward'       => ['nullable', 'boolean'],
            'reward_amount'    => ['nullable', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'city'             => ['nullable', 'string', 'max:50'],
            'event_address'    => ['nullable', 'string', 'max:100'],
            'latitude'         => ['nullable', 'numeric', 'regex:/^-?\d+(\.\d{1,8})?$/'],
            'longitude'        => ['nullable', 'numeric', 'regex:/^-?\d+(\.\d{1,8})?$/'],
            'event_date'       => ['required', 'date', 'before_or_equal:today'],
            'photos'           => [
                'required',
                'array',
                'min:1',
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
            'report_type_id'   => $this->input('report_type_id', 1), 
            'report_status_id' => $this->input('report_status_id', 1),
            'has_reward'       => $this->boolean('has_reward')
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
            'phone_home.max'              => 'El teléfono convencional no debe superar los 10 dígitos.',
            'phone_home.regex'            => 'El teléfono convencional solo debe contener números (7 a 10 dígitos).',
            'phone_mobile.max'            => 'El teléfono celular no debe superar los 15 caracteres.',
            'phone_mobile.regex'          => 'El teléfono celular debe tener un formato válido (ej. +593 962618451 o 0962618451).',
            'reward_amount.regex'         => 'El monto de la recompensa debe tener máximo 2 decimales',
            'latitude.regex'              => 'La latitud debe ser un número válido con máximo 8 decimales',
            'longitude.regex'             => 'La longitud debe ser un número válido con máximo 8 decimales',
            'event_date.required'         => 'La fecha del suceso es requerida',
            'event_date.before_or_equal'  => 'La fecha del suceso no puede ser futura',
            'photos.required'             => 'Las fotos son obligatorias',
            'photos.array'                => 'El formato de las fotos es inválido',
            'photos.min'                  => 'Debe adjuntar mínimo 1 foto',
            'photos.max'                  => 'No puede adjuntar más de 5 fotos',
            'photos.*.path_temp.required' => 'El archivo temporal de la imagen es obligatorio',
            'photos.*.path_temp.string'   => 'El archivo temporal de la imagen debe ser una cadena válida',
            'photos.*.is_main.required'   => 'Debe indicar cuál foto es la principal',
            'photos.*.is_main.boolean'    => 'El campo principal debe ser verdadero o falso',
        ];
    }
}