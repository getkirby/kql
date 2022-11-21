<?php

namespace Kirby\Kql;

use Kirby\Toolkit\A;
use ReflectionClass;
use ReflectionMethod;

/**
 * Providing help information about
 * queried objects, methods, arrays...
 *
 * @package   Kirby KQL
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      https://getkirby.com
 * @copyright Bastian Allgeier
 * @license   https://getkirby.com/license
 */
class Help
{
	/**
	 * Provides information about passed value
	 * depending on its type
	 */
	public static function for($value): array
	{
		if (is_array($value) === true) {
			return static::forArray($value);
		}

		if (is_object($value) === true) {
			return static::forObject($value);
		}

		return [
			'type'  => gettype($value),
			'value' => $value
		];
	}

	/**
	 * @internal
	 */
	public static function forArray(array $array): array
	{
		return [
			'type'  => 'array',
			'keys'  => array_keys($array),
		];
	}

	/**
	 * Gathers information for method about
	 * name, parameters, return type etc.
	 * @internal
	 */
	public static function forMethod(object $object, string $method): array
	{
		$reflection = new ReflectionMethod($object, $method);
		$returns    = $reflection->getReturnType()?->getName();
		$params     = [];

		foreach ($reflection->getParameters() as $param) {
			$name     = $param->getName();
			$required = $param->isOptional() === false;
			$type     = $param->hasType() ? $param->getType()->getName() : null;
			$default  = null;

			if ($param->isDefaultValueAvailable()) {
				$default = $param->getDefaultValue();
			}

			$call = '';

			if ($type !== null) {
				$call = $type . ' ';
			}

			$call .= '$' . $name;

			if ($required === false && $default !== null) {
				$call .= ' = ' . var_export($default, true);
			}

			$p['call'] = $call;

			$params[$name] = compact('name', 'type', 'required', 'default', 'call');
		}

		$call = '.' . $method;

		if (empty($params) === false) {
			$call .= '(' . implode(', ', array_column($params, 'call')) . ')';
		}

		return [
			'call'    => $call,
			'name'    => $method,
			'params'  => $params,
			'returns' => $returns
		];
	}

	/**
	 * Gathers informations for each unique method
	 * @internal
	 */
	public static function forMethods(object $object, array $methods): array
	{
		$methods    = array_unique($methods);
		$reflection = [];

		sort($methods);

		foreach ($methods as $methodName) {
			if (method_exists($object, $methodName) === false) {
				continue;
			}

			$reflection[$methodName] = static::forMethod($object, $methodName);
		}

		return $reflection;
	}

	/**
	 * Retrieves info for objects either from Interceptor (to
	 * only list allowed methods) or via reflection
	 * @internal
	 */
	public static function forObject(object $object): array
	{
		// get interceptor object to only return info on allowed methods
		$interceptor = Interceptor::replace($object);

		if ($interceptor instanceof Interceptor) {
			return $interceptor->__debugInfo();
		}

		// for original classes, use reflection
		$class   = new ReflectionClass($object);
		$methods = A::map(
			$class->getMethods(),
			fn ($method) => static::forMethod($object, $method->getName())
		);

		return [
			'type'    => $class->getName(),
			'methods' => $methods
		];
	}
}
