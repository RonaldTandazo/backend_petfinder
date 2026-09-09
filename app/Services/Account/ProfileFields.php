<?php

namespace App\Services\Account;

class ProfileFields
{
    public const FIELDS_BY_TYPE = [
        'user' => [
            'first_names'        => ['required', 'string', 'max:50'],
            'last_names'         => ['required', 'string', 'max:50'],
            'email'              => ['required', 'string', 'email', 'max:50'],
            'gender_id'          => ['nullable', 'integer', 'exists:genders,id']
        ],
        'shelter' => [
            'name'               => ['required', 'string', 'max:100'],
            'business_name'      => ['nullable', 'string', 'max:100'],
            'tax_identification' => ['nullable', 'string', 'max:50'],
            'email'              => ['required', 'string', 'email', 'max:550'],
            'latitude'           => ['nullable', 'numeric', 'regex:/^-?\d+(\.\d{1,8})?$/'],
            'longitude'          => ['nullable', 'numeric', 'regex:/^-?\d+(\.\d{1,8})?$/'],
            'web_page'           => ['nullable', 'string', 'url', 'max:150'],
            'business_hours'     => ['nullable', 'string', 'max:100'],
        ],
    ];

    public static function fieldsFor(string $type): array
    {
        return self::FIELDS_BY_TYPE[$type];
    }
}