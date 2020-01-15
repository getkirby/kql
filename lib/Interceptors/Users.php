<?php

namespace Kirby\Kql\Interceptors;

class Users extends Collection
{

    const CLASS_ALIAS = 'users';

    public function allowedMethods(): array
    {
        return array_merge(
            parent::allowedMethods(),
            [
                'role'
            ]
        );
    }

}
