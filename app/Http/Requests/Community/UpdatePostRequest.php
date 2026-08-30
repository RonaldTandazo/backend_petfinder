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
            'title'        => ['sometimes', 'nullable', 'string', 'max:140'],
            'content'      => ['sometimes', 'nullable', 'string', 'max:2000'],
            'news_type_id' => ['sometimes', 'nullable', 'integer', 'exists:news_types,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $empty = blank($this->input('title'))
                && blank($this->input('content'))
                && blank($this->input('news_type_id'));

            if ($empty) {
                $validator->errors()->add('title', 'Debe enviar al menos un campo para actualizar');
            }
        });
    }

    public function messages(): array
    {
        return [
            'title.max'           => 'El título no puede superar los 140 caracteres',
            'content.max'         => 'El contenido no puede superar los 2000 caracteres',
            'news_type_id.exists' => 'El tipo de publicación seleccionado no es válido',
        ];
    }
}
