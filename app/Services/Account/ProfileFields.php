<?php

namespace App\Services\Account;

class ProfileFields
{
    public const FIELDS_BY_TYPE = [
        'user' => [
            'first_names' => ['nullable', 'string', 'max:100'],
            'last_names'  => ['nullable', 'string', 'max:100'],
            'telephone'   => ['nullable', 'string', 'max:20'],
            'country_id'  => ['nullable', 'integer', 'exists:countries,id'],
            'gender_id'   => ['nullable', 'integer', 'exists:genders,id'],
            'city'        => ['nullable', 'string', 'max:100'],
            'address'     => ['nullable', 'string', 'max:255'],
            'avatar'      => ['nullable', 'string', 'max:255'],
        ],
        'shelter' => [
            'name'               => ['nullable', 'string', 'max:100'],
            'business_name'      => ['nullable', 'string', 'max:100'],
            'tax_identification' => ['nullable', 'string', 'max:50'],
            'telephone'          => ['nullable', 'string', 'max:20'],
            'physical_address'   => ['nullable', 'string', 'max:255'],
            'country_id'         => ['nullable', 'integer', 'exists:countries,id'],
            'city'               => ['nullable', 'string', 'max:100'],
            'latitude'           => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'          => ['nullable', 'numeric', 'between:-180,180'],
            'web_page'           => ['nullable', 'string', 'url', 'max:150'],
            'business_hours'     => ['nullable', 'string', 'max:100'],
            'logo'               => ['nullable', 'string', 'max:255'],
        ],
    ];

    public static function fieldsFor(string $type): array
    {
        return self::FIELDS_BY_TYPE[$type];
    }
}