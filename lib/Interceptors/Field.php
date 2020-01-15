<?php

namespace Kirby\Kql\Interceptors;

class Field extends Interceptor
{

    const CLASS_ALIAS = 'field';

    public function allowedMethods(): array
    {
        return array_merge(
            array_keys($this->object::$methods),
            [
                'exists',
                'isEmpty',
                'isNotEmpty',
                'key',
                'or',
                'value'
            ]
        );
    }

}
