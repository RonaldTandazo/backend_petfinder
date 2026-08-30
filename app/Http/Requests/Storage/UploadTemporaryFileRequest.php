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
        $filesCount = is_array($this->file('files')) ? count($this->file('files')) : 0;

        return [
            'files'   => ['required', 'array', 'min:1'],
            'files.*' => ['required', 'image', 'max:5120'],
            'uuids'   => ['required', 'array', "size:{$filesCount}"],
            'uuids.*' => ['required', 'string', 'uuid'],
        ];
    }

    public function messages(): array
    {
        return [
            'files.required'   => 'Debe adjuntar al menos una imagen.',
            'files.array'      => 'El campo de imágenes debe ser una lista.',
            'files.min'        => 'Debe enviar al menos 1 imagen.',
            'files.*.required' => 'La imagen es requerida.',
            'files.*.image'    => 'Cada archivo debe ser una imagen válida (jpg, png, webp, etc.).',
            'files.*.max'      => 'Ninguna imagen puede pesar más de 5 MB.',
            'uuids.required'   => 'Debe adjuntar la lista de identificadores UUID.',
            'uuids.array'      => 'Los UUIDs deben ser una lista.',
            'uuids.size'       => 'La cantidad de UUIDs debe coincidir exactamente con la cantidad de imágenes.',
            'uuids.*.required' => 'El UUID es obligatorio.',
            'uuids.*.uuid'     => 'El formato del UUID no es válido.',
        ];
    }
}
