<?php

namespace App\Http\Requests\Community;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->tutor !== null;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'El contenido del comentario es obligatorio',
            'content.max'      => 'El comentario no puede superar los 500 caracteres',
        ];
    }
}
