<?php

namespace App\Http\Requests\Legal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class LegalDocumentVersionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->tutor !== null;
    }

    public function rules(): array
    {
        return [
            'new_documents'                 => ['required', 'array', 'min:1'],
            'new_documents.*.tag'           => ['required', 'string', 'distinct', 'exists:legal_documents,tag'],
            'new_documents.*.version'       => ['required', 'string', 'max:10'],
            'new_documents.*.markdown_file' => ['required', 'file', 'extensions:md,markdown', 'max:5120'],
            'new_documents.*.pdf_file'      => ['required', 'file', File::types(['pdf'])->max(5120)],
        ];
    }

    public function messages(): array
    {
        return [
            'new_documents.required'                 => 'Debes indicar los nuevos documentos',
            'new_documents.array'                    => 'Los nuevos documentos deben enviarse como una lista',
            'new_documents.min'                      => 'Debes enviar al menos un documento',
            
            'new_documents.*.tag.required'           => 'El identificador de cada documento es requerido',
            'new_documents.*.tag.distinct'           => 'No puedes enviar documentos duplicados',
            'new_documents.*.tag.exists'             => 'Un documento indicado no es válido',
            
            'new_documents.*.version.required'       => 'La versión de cada documento es requerida',
            'new_documents.*.version.max'            => 'La versión no puede superar los 10 caracteres',
            
            'new_documents.*.markdown_file.required' => 'El archivo Markdown es requerido',
            'new_documents.*.markdown_file.file'     => 'El Markdown enviado no es un archivo válido',
            'new_documents.*.markdown_file.max'      => 'El Markdown no puede superar los 5 MB',

            'new_documents.*.pdf_file.required'      => 'El archivo PDF es requerido',
            'new_documents.*.pdf_file.file'          => 'El PDF enviado no es un archivo válido',
            'new_documents.*.pdf_file.max'           => 'El PDF no puede superar los 5 MB',
        ];
    }
}