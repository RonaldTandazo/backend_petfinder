<?php

namespace App\Http\Requests\Follow;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FollowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->tutor !== null;
    }

    public function rules(): array
    {
        return [
            'category' => ['required', 'string', Rule::in(['all', 'adoption', 'lost_pet'])],
            'page'     => ['nullable', 'integer', 'min:1'],
            'limit'    => ['nullable', 'integer', 'min:1', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'category.required' => 'La categoría es obligatorio',
            'category.in'       => 'La categoría no es válida',
            'page.integer'      => 'El número de página debe ser un valor entero',
            'page.min'          => 'La página debe ser al menos 1',
            'limit.integer'     => 'El límite debe ser un valor entero',
            'limit.min'         => 'El límite mínimo permitido es 1',
            'limit.max'         => 'El límite máximo permitido es 50',
        ];
    }
}