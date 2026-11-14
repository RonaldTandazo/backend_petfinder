<?php

namespace App\Http\Requests\Adoption;

use Illuminate\Foundation\Http\FormRequest;

class ChangeStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->tutor !== null;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'exists:pet_statuses,tag'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Debe indicar el estado',
            'status.exists'   => 'El estado indicado no existe',
        ];
    }
}