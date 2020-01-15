<?php

namespace Kirby\Kql\Interceptors;

use Exception;

class User extends Model
{

    const CLASS_ALIAS = 'user';

    protected $toArray = [
        'id',
        'name',
        'role',
        'username'
    ];

    public function allowedMethods(): array
    {
        return array_merge(
            $this->allowedMethodsForFiles(),
            $this->allowedMethodsForModels(),
            [
                'avatar',
                'email',
                'id',
                'isAdmin',
                'language',
                'modified',
                'name',
                'role',
                'username',
            ]
        );
    }

}
