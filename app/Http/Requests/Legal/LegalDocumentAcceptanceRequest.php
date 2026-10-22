<?php

namespace App\Http\Requests\Legal;

use Illuminate\Foundation\Http\FormRequest;

class LegalDocumentAcceptanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->tutor !== null;
    }

    public function rules(): array
    {
        return [
            'accepted_documents'   => ['required', 'array', 'min:1'],
            'accepted_documents.*' => ['required', 'string', 'distinct', 'exists:legal_documents,tag'],
        ];
    }

    public function messages(): array
    {
        return [
            'accepted_documents.required'   => 'Debes indicar los documentos que aceptas.',
            'accepted_documents.array'      => 'Los documentos aceptados deben enviarse como una lista.',
            'accepted_documents.min'        => 'Debes aceptar al menos un documento.',
            'accepted_documents.*.distinct' => 'No puedes enviar documentos duplicados.',
            'accepted_documents.*.exists'   => 'El documento indicado no es válido.',
        ];
    }
}