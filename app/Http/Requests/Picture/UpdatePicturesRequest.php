<?php

namespace App\Http\Requests\Picture;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePicturesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->tutor !== null;
    }

    public function rules(): array
    {
        return [
            'pictures'             => ['required', 'array', 'min:1'],
            'pictures.*.id'        => ['required', 'integer'],
            'pictures.*.path_temp' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'pictures.required'             => 'Debe indicar al menos una foto a actualizar.',
            'pictures.array'                => 'El formato de las fotos es inválido.',
            'pictures.*.id.required'        => 'Cada foto debe indicar su id.',
            'pictures.*.path_temp.required' => 'Cada foto debe indicar el archivo temporal a usar.',
        ];
    }
}
