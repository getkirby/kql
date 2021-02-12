<?php

namespace Kirby\Kql\Interceptors\Cms;

class Layouts extends Collection
{
    const CLASS_ALIAS = 'layouts';

    public function toArray(): array
    {
        return $this->object->toArray();
    }
}
