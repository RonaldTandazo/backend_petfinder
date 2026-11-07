<?php

namespace App\Http\Requests\Follow;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OnFollowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->tutor !== null;
    }

    public function rules(): array
    {
        $table = match ($this->input('pet_type')) {
            'adoption' => 'pets',
            'lost_pet' => 'lost_pets',
            default => null,
        };

        return [
            'pet_id'       => ['required', 'integer', ...($table ? [Rule::exists($table, 'id')] : []),],
            'pet_type'     => ['required', 'string', Rule::in(['adoption', 'lost_pet'])],
            'is_following' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'pet_id.required'       => 'La mascota es obligatoria',
            'pet_id.integer'        => 'El identificador de la mascota no es válido',
            'pet_id.exists'         => 'La mascota no existe',
            
            'pet_type.required'     => 'El tipo de mascota es obligatorio',
            'pet_type.in'           => 'El tipo de mascota no es válido',
            
            'is_following.required' => 'El estado de seguimiento es obligatorio',
            'is_following.boolean'  => 'El estado de seguimiento debe ser un valor booleano',
        ];
    }
}