<?php

namespace Kirby\Kql;

use Exception;
use Kirby\Toolkit\Str;

class Interceptor
{
    public static function replace($object)
    {
        $className = get_class($object);

        if (Str::startsWith($className, 'Kirby\\Cms\\') === false) {
            return $object;
        }

        $interceptor = str_replace('Kirby\\Cms\\', 'Kirby\\Kql\\Interceptors\\', $className);

        if (class_exists($interceptor) === true) {
            return new $interceptor($object);
        } elseif (is_a($object, 'Kirby\Cms\Collection') === true) {
            return new Interceptors\Collection($object);
        } elseif (is_a($object, 'Kirby\Cms\Page') === true) {
            return new Interceptors\Page($object);
        } elseif (is_a($object, 'Kirby\Cms\Blueprint') === true) {
            return new Interceptors\Blueprint($object);
        }

        throw new Exception('Unknown object: ' . $className);
    }
}
