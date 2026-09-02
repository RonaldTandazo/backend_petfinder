<?php

namespace App\Http\Requests\Community;

use App\Models\Community\Post;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        $account = $this->user();

        if (!$account || !$account->tutor) return false;
        
        return Post::where('_id', $this->route('postId'))
            ->where('author.tutor_id', $account->tutor->id)
            ->exists();
    }

    public function rules(): array
    {
        return [
            'title'              => ['sometimes', 'nullable', 'string', 'max:140'],
            'content'            => ['sometimes', 'nullable', 'string', 'max:2000'],
            'news_type_id'       => ['sometimes', 'nullable', 'integer', 'exists:news_types,id'],
            'images'             => ['sometimes', 'array', 'max:1'],
            'images.*.path'      => ['nullable', 'string', 'max:255'],
            'images.*.path_temp' => ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z0-9\-]+\.[A-Za-z0-9]+$/'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $empty = blank($this->input('title'))
                && blank($this->input('content'))
                && blank($this->input('news_type_id'))
                && !$this->has('images');

            if ($empty) {
                $validator->errors()->add('title', 'Debe enviar al menos un campo para actualizar');
            }

            foreach ($this->input('images', []) as $index => $image) {
                if (blank($image['path'] ?? null) && blank($image['path_temp'] ?? null)) {
                    $validator->errors()->add(
                        "images.{$index}",
                        'Cada imagen debe indicar su path y/o path_temp'
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'title.max'                => 'El título no puede superar los 140 caracteres',
            'content.max'              => 'El contenido no puede superar los 2000 caracteres',
            'news_type_id.exists'      => 'El tipo de publicación seleccionado no es válido',
            'images.max'               => 'Máximo 1 imagen por publicación',
            'images.*.path.max'        => 'El path de la imagen no puede superar los 255 caracteres',
            'images.*.path_temp.max'   => 'El nombre del archivo de imagen no puede superar los 255 caracteres',
            'images.*.path_temp.regex' => 'La imagen debe ser un archivo temporal válido',
        ];
    }
}
