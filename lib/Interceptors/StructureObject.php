<?php

namespace Kirby\Kql\Interceptors;

use Exception;

class StructureObject extends Model
{

    const CLASS_ALIAS = 'structureItem';

    public function allowedMethods(): array
    {
        return array_merge(
            $this->allowedMethodsForSiblings(),
            [
                'content',
                'id',
                'parent',
            ]
        );
    }

}
