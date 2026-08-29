<?php

namespace App\Http\Requests\Storage;

use Illuminate\Foundation\Http\FormRequest;

class UploadTemporaryFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->tutor !== null;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'image', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Debe adjuntar un archivo.',
            'file.image'    => 'El archivo debe ser una imagen (jpg, png, webp, gif).',
            'file.max'      => 'La imagen no puede pesar más de 5 MB.',
        ];
    }
}
