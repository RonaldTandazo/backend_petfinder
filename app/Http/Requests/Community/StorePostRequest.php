<?php

namespace App\Http\Requests\Community;

use App\Services\Community\CommunityAuthorService;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        $account = $this->user();

        if (!$account || !$account->tutor) return false;
        

        return in_array(
            CommunityAuthorService::typeOf($account),
            CommunityAuthorService::ALLOWED_PUBLISHERS
        );
    }

    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'max:140'],
            'content'      => ['required', 'string', 'max:2000'],
            'news_type_id' => ['required', 'integer', 'exists:news_types,id'],
            'images'       => ['required', 'array', 'min:1', 'max:1'],
            'images.*'     => ['required', 'string', 'max:255', 'regex:/^[A-Za-z0-9\-]+\.[A-Za-z0-9]+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'        => 'El título de la publicación es obligatorio',
            'title.max'             => 'El título no puede superar los 140 caracteres',
            'content.required'      => 'El contenido de la publicación es obligatorio',
            'content.max'           => 'El contenido no puede superar los 2000 caracteres',
            'news_type_id.required' => 'Debe seleccionar el tipo de publicación',
            'news_type_id.exists'   => 'El tipo de publicación seleccionado no es válido',
            'images.required'       => 'Debe subir al menos una imagen',
            'images.min'            => 'Debe subir al menos una imagen',
            'images.max'            => 'Máximo 1 imagen por publicación',
            'images.*.string'       => 'Cada imagen debe ser un archivo temporal válido',
            'images.*.max'          => 'El nombre del archivo de imagen no puede superar los 255 caracteres',
            'images.*.regex'        => 'La imagen debe ser un archivo temporal válido',
        ];
    }
}
