<?php

namespace Kirby\Kql\Interceptors;

class Content extends Interceptor
{
    public function __call($method, array $args = [])
    {
        if ($this->isAllowedMethod($method) === true) {
            return $this->object->$method(...$args);
        }

        if (method_exists($this->object, $method) === false) {
            return $this->object->get($method);
        }

        $this->forbiddenMethod($method);
    }

    public function allowedMethods(): array
    {
        return [
            'data',
            'fields',
            'has',
            'get',
            'keys',
            'not',
        ];
    }
}
