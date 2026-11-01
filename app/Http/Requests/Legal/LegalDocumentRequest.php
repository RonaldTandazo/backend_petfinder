<?php

namespace App\Http\Requests\Legal;

use Illuminate\Foundation\Http\FormRequest;

class LegalDocumentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'tag' => ['required', 'string', 'exists:legal_documents,tag'],
        ];
    }

    public function messages(): array
    {
        return [
            'new_documents.*.tag.required' => 'El identificador del documento es requerido',
            'new_documents.*.tag.exists'   => 'El documento indicado no es válido',
        ];
    }
}