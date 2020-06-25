<?php

namespace Kirby\Kql\Interceptors;

class Role extends Interceptor
{
    const CLASS_ALIAS = 'role';

    protected $toArray = [
        'description',
        'id',
        'name',
        'title',
    ];

    public function allowedMethods(): array
    {
        return [
            'description',
            'id',
            'name',
            'permissions',
            'title'
        ];
    }

    public function permissions(): array
    {
        return $this->object->permissions()->toArray();
    }
}
