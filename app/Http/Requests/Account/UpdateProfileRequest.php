<?php

namespace App\Http\Requests\Account;

use App\Models\Shelter;
use App\Services\Account\ProfileFields;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->tutor;
    }

    public function rules(): array
    {
        $type = $this->user() instanceof Shelter ? 'shelter' : 'user';

        return ProfileFields::fieldsFor($type);
    }

    public function messages(): array
    {
        return [
            'country_id.exists'  => 'El país seleccionado no es válido',
            'gender_id.exists'   => 'El género seleccionado no es válido',
            'web_page.url'       => 'El formato de la página web debe ser una URL válida',
            'latitude.between'   => 'La latitud debe estar dentro de un rango geográfico válido',
            'longitude.between'  => 'La longitud debe estar dentro de un rango geográfico válido',
        ];
    }
}