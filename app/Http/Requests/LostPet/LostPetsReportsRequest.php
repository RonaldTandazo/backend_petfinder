<?php

namespace App\Http\Requests\LostPet;

use Illuminate\Foundation\Http\FormRequest;

class LostPetsReportsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->tutor !== null;
    }

    public function rules(): array
    {
        return [
            'search'    => ['nullable', 'string', 'max:100'],
            'species'   => ['nullable', 'array'],
            'species.*' => ['integer', 'exists:species,id'],
            'genders'   => ['nullable', 'array'],
            'genders.*' => ['integer', 'exists:animal_gender,id'],
            'sizes'     => ['nullable', 'array'],
            'sizes.*'   => ['integer', 'exists:sizes,id'],
            'page'      => ['nullable', 'integer', 'min:1'],
            'limit'     => ['nullable', 'integer', 'min:1', 'max:50'],
            'tutor_id'  => ['nullable', 'integer', 'exists:tutors,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'search.max'        => 'El término de búsqueda no puedo superar los 100 caracteres',
            'species.array'     => 'El formato de las especies debe ser una lista',
            'species.*.integer' => 'Cada especie debe ser un identificador válido',
            'species.*.exists'  => 'Una o más especies seleccionadas no existen',
            'genders.array'     => 'El formato de los géneros debe ser una lista',
            'genders.*.integer' => 'Cada género debe ser un identificador válido',
            'genders.*.exists'  => 'Uno o más géneros seleccionados no existen',
            'sizes.array'       => 'El formato de los tamaños debe ser una lista',
            'sizes.*.integer'   => 'Cada tamaño debe ser un identificador válido',
            'sizes.*.exists'    => 'Uno o más tamaños seleccionados no existen',
            'page.integer'      => 'El número de página debe ser un valor entero',
            'page.min'          => 'La página debe ser al menos 1',
            'limit.integer'     => 'El límite debe ser un valor entero',
            'limit.min'         => 'El límite mínimo permitido es 1',
            'limit.max'         => 'El límite máximo permitido es 50',
            'tutor_id.exists'   => 'El tutor especificado no existe'
        ];
    }
}