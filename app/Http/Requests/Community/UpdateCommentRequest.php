<?php

namespace App\Http\Requests\Community;

use App\Models\Community\Comment;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $account = $this->user();

        if (!$account || !$account->tutor) return false;

        return Comment::where('_id', $this->route('commentId'))
            ->where('author.tutor_id', $account->tutor->id)
            ->exists();
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
