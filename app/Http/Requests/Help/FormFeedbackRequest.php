<?php

namespace App\Http\Requests\Help;

use Illuminate\Foundation\Http\FormRequest;

class FormFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->tutor !== null;
    }

    public function rules(): array
    {
        return [
            'subject'                => ['required', 'string', 'max:100'],
            'comment'             => ['required', 'string', 'max:500'],
            'photos'              => ['nullable', 'array', 'max:5',],
            'photos.*.path_temp'  => ['required', 'string', 'max:255'],
            'photos.*.is_main'    => ['required', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'help_status_id' => $this->input('help_status_id', 1),
        ]);
    }

    public function messages(): array
    {
        return [
            'subject.required'              => 'El tema es obligatorio',
            'comment.required'              => 'El comentario es obligatorio',
            'photos.array'                  => 'El formato de las fotos es inválido',
            'photos.max'                    => 'No puede adjuntar más de 5 fotos',
            'photos.*.path_temp.required'   => 'El archivo temporal de la imagen es obligatorio',
            'photos.*.path_temp.string'     => 'El archivo temporal de la imagen debe ser una cadena válida',
            'photos.*.is_main.required'     => 'Debe indicar cuál foto es la principal',
            'photos.*.is_main.boolean'      => 'El campo principal debe ser verdadero o falso',
        ];
    }
}