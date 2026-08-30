<?php

namespace App\Services\Community;

use App\Models\Shelter;
use Illuminate\Contracts\Auth\Authenticatable;

class CommunityAuthorService
{
    public const TYPES = ['user', 'shelter'];

    public const ALLOWED_PUBLISHERS = ['shelter'];

    public static function typeOf(Authenticatable $account): string
    {
        return $account instanceof Shelter ? 'shelter' : 'user';
    }

    public function build(Authenticatable $account): array
    {
        $isShelter = $account instanceof Shelter;

        return [
            'tutor_id'     => $account->tutor?->id,
            'tutor_type'   => self::typeOf($account),
            'account_id'   => $account->id,
            'display_name' => $isShelter ? $account->name
                                         : trim("{$account->first_names} {$account->last_names}"),
            'avatar'       => $isShelter ? $account->logo : $account->avatar,
        ];
    }
}
