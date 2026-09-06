<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->tutor;
    }

    public function rules(): array
    {
        return [
            'page_adoptions' => ['nullable', 'integer', 'min:1'],
            'page_lost_pets' => ['nullable', 'integer', 'min:1'],
            'limit'          => ['nullable', 'integer', 'min:1', 'max:50'],
            'tutor_id'       => ['nullable', 'integer', 'exists:tutors,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'page_adoptions' => $this->input('page_adoptions', 1),
            'page_lost_pets' => $this->input('page_lost_pets', 1),
            'limit'          => $this->input('limit', 20),
        ]);
    }

    public function messages(): array
    {
        return [
            'page_adoptions.integer' => 'El número de página de adopciones debe ser un valor entero',
            'page_adoptions.min'     => 'La página de adopciones debe ser al menos 1',
            'page_lost_pets.integer' => 'El número de página de mascotas perdidas debe ser un valor entero',
            'page_lost_pets.min'     => 'La página  mascotas perdidas debe ser al menos 1',
            'limit.integer'          => 'El límite debe ser un valor entero',
            'limit.min'              => 'El límite mínimo permitido es 1',
            'limit.max'              => 'El límite máximo permitido es 50',
            'tutor_id.exists'        => 'El tutor especificado no existe'
        ];
    }
}