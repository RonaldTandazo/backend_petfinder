<?php

namespace App\Http\Requests\LostPet;

use Illuminate\Foundation\Http\FormRequest;

class FormLostPetEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->tutor !== null;
    }

    public function rules(): array
    {
        return [
            'lost_pet_id'            => ['required', 'integer', 'exists:lost_pets,id'],
            'lost_pet_event_type_id' => ['nullable', 'integer', 'exists:lost_pet_event_types,id'],
            'event_date'             => ['required', 'date', 'before_or_equal:today'],
            'event_address'          => ['required', 'string', 'max:100'],
            'latitude'               => ['nullable', 'numeric', 'regex:/^-?\d+(\.\d{1,8})?$/'],
            'longitude'              => ['nullable', 'numeric', 'regex:/^-?\d+(\.\d{1,8})?$/'],
            'comment'                => ['nullable', 'string', 'max:500'],
            'photos'                 => [
                'nullable',
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

    public function messages(): array
    {
        return [
            'lost_pet_id.exists'            => 'La mascota perdida seleccionada no es válida',
            'lost_pet_event_type_id.exists' => 'El tipo de evento seleccionado no es válido',
            'event_date.required'           => 'La fecha del suceso es requerida',
            'event_date.before_or_equal'    => 'La fecha del suceso no puede ser futura',
            'event_address.required'        => 'La dirección del suceso es requerida',
            'latitude.regex'                => 'La latitud debe ser un número válido con máximo 8 decimales',
            'longitude.regex'               => 'La longitud debe ser un número válido con máximo 8 decimales',
            'photos.array'                  => 'El formato de las fotos es inválido',
            'photos.min'                    => 'Debe adjuntar mínimo 1 foto',
            'photos.max'                    => 'No puede adjuntar más de 5 fotos',
            'photos.*.path_temp.required'   => 'El archivo temporal de la imagen es obligatorio',
            'photos.*.path_temp.string'     => 'El archivo temporal de la imagen debe ser una cadena válida',
            'photos.*.is_main.required'     => 'Debe indicar cuál foto es la principal',
            'photos.*.is_main.boolean'      => 'El campo principal debe ser verdadero o falso',
        ];
    }
}