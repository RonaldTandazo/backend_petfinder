<?php

namespace App\Http\Requests\LostPet;

use Illuminate\Foundation\Http\FormRequest;

class LostPetFollowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->tutor !== null;
    }

    public function rules(): array
    {
        return [
            'is_following'  => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'is_following.required' => 'El estado de seguimiento es obligatorio',
            'is_following.boolean' => 'El estado de seguimiento debe ser un valor booleano',
        ];
    }
}