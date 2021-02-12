<?php

namespace Kirby\Kql\Interceptors\Cms;

class LayoutColumns extends Collection
{
    const CLASS_ALIAS = 'layoutColumns';

    public function toArray(): array
    {
        return $this->object->toArray();
    }
}
