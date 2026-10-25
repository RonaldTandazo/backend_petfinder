<?php

namespace App\Http\Requests\Account;

use App\Models\Account\User;
use App\Services\Account\ProfileFields;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->tutor;
    }

    public function rules(): array
    {
        $account = $this->user();

        $type = $account instanceof User ? 'user' : 'shelter';

        $typeFields = ProfileFields::fieldsFor($type);

        $typeFields['email'][] = Rule::unique($type === 'user' ? 'users' : 'shelters', 'email')->ignore($account->id);

        $commonFields = [
            'phone_mobile'        => ['nullable', 'string', 'max:15', 'regex:/^\+?[0-9]* ?[0-9]*$/'],
            'country_id'          => ['nullable', 'integer', 'exists:countries,id'],
            'city'                => ['nullable', 'string', 'max:25'],
            'address'             => ['nullable', 'string', 'max:100'],
            'avatar'              => ['nullable', 'array', 'max:1',],
            'avatar.*.type'       => ['required', 'string', 'in:existing,new'],
            'avatar.*.id'         => ['nullable', 'integer', 'required_if:avatar.*.type,existing'],
            'avatar.*.uuid'       => ['nullable', 'string', 'max:255', 'required_if:avatar.*.type,new'],
            'avatar.*.path_temp'  => [ 'nullable', 'string', 'max:255', 'required_if:avatar.*.type,new'],
            'avatar.*.is_main'    => ['required', 'boolean'],
            'avatar.*.sort_order' => ['required', 'integer', 'min:0', 'max:0']
        ];

        return array_merge($typeFields, $commonFields);
    }

    public function messages(): array
    {
        return [
            // USER
            'first_names.required'           => 'Los nombres son obligatorios',
            'first_names.max'                => 'Los nombres no pueden superar los 50 caracteres',
            'last_names.required'            => 'Los apellidos son obligatorios',
            'last_names.max'                 => 'Los apellidos no pueden superar los 50 caracteres',
            'gender_id.exists'               => 'El género seleccionado no es válido',
            
            // SHELTER
            'name.required'                  => 'El nombre del refugio es obligatorio',
            'name.max'                       => 'El nombre del refugio no puede superar los 100 caracteres',
            'business_name.max'              => 'La razón social no puede superar los 100 caracteres',
            'tax_identification.max'         => 'La identificación tributaria no puede superar los 50 caracteres',
            'latitude.regex'                 => 'La latitud debe ser un número válido con máximo 8 decimales',
            'longitude.regex'                => 'La longitud debe ser un número válido con máximo 8 decimales',
            'web_page.url'                   => 'El formato de la página web debe ser una URL válida',
            'web_page.max'                   => 'La página web no puede superar los 150 caracteres',
            'business_hours.max'             => 'El horario de atención no puede superar los 100 caracteres',

            // COMMON
            'email.required'                 => 'El correo electrónico es obligatorio',
            'email.email'                    => 'El correo electrónico debe tener un formato válido',
            'email.max'                      => 'El correo electrónico no puede superar los 50 caracteres',
            'email.unique'                   => 'El correo electrónico ya se encuentra registrado',
            'phone_mobile.max'               => 'El teléfono celular no debe superar los 15 caracteres',
            'phone_mobile.regex'             => 'El teléfono celular debe tener un formato válido (ej. +593 962618451 o 0962618451)',
            'address.max'                    => 'La dirección no puede superar los 100 caracteres',
            'country_id.exists'              => 'El país seleccionado no es válido',
            'city.max'                       => 'La ciudad no puede superar los 25 caracteres',
            'avatar.array'                   => 'El formato de la foto es inválido',
            'avatar.max'                     => 'No puede adjuntar más de 1 foto',
            'avatar.*.type.required'         => 'Debe indicar el tipo de imagen',
            'avatar.*.type.in'               => 'El tipo de imagen no es válido',
            'avatar.*.id.required_if'        => 'El identificador de la imagen existente es obligatorio',
            'avatar.*.id.integer'            => 'El identificador de la imagen no es válido',
            'avatar.*.uuid.required_if'      => 'El identificador de la imagen nueva es obligatorio',
            'avatar.*.path_temp.required_if' => 'El archivo temporal de la imagen es obligatorio',
            'avatar.*.path_temp.string'      => 'El archivo temporal de la imagen debe ser una cadena válida',
            'avatar.*.is_main.required'      => 'Debe indicar cuál foto es la principal',
            'avatar.*.is_main.boolean'       => 'El campo principal debe ser verdadero o falso',
            'avatar.*.sort_order.required'   => 'El orden de la imagen es obligatorio',
            'avatar.*.sort_order.integer'    => 'El orden de la imagen no es válido',
            'avatar.*.sort_order.min'        => 'El orden de la imagen no puede ser negativo',
            'avatar.*.sort_order.max'        => 'El orden de la imagen no puede ser mayor a 1'
        ];
    }
}