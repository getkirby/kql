<?php

namespace Kirby\Kql\Interceptors\Content;

use Kirby\Cms\HasMethods;
use Kirby\Content\FieldMethods;
use Kirby\Kql\Interceptor;
use ReflectionClass;
use ReflectionMethod;

class Field extends Interceptor
{
	public const CLASS_ALIAS = 'field';

	/**
	 * Core field methods that must not be
	 * called in the API context
	 */
	public const BLOCKED_FIELD_METHODS = [
		'toenum'
	];

	protected static array|null $fieldMethods = null;

	public function __call($method, array $args = [])
	{
		if ($this->isAllowedMethod($method) === true) {
			return $this->object->$method(...$args);
		}

		// core field methods
		if (in_array(strtolower($method), static::fieldMethods(), true) === true) {
			return $this->object->$method(...$args);
		}

		// custom field methods registered by plugins
		if (
			method_exists($this->object, $method) === false &&
			$this->object->hasMethod(strtolower($method)) === true
		) {
			return $this->object->$method(...$args);
		}

		$this->forbiddenMethod($method);
	}

	public function allowedMethods(): array
	{
		return [
			'exists',
			'isEmpty',
			'isNotEmpty',
			'key',
			'or',
			'value'
		];
	}

	/**
	 * Returns the lowercase names of all core field methods
	 * from the `FieldMethods` trait, without the internal
	 * `HasMethods` helpers and blocked methods
	 * @internal
	 */
	public static function fieldMethods(): array
	{
		if (static::$fieldMethods !== null) {
			return static::$fieldMethods;
		}

		$helpers = array_map(
			fn (ReflectionMethod $method) => strtolower($method->getName()),
			(new ReflectionClass(HasMethods::class))->getMethods()
		);

		$methods = [];

		foreach ((new ReflectionClass(FieldMethods::class))->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
			$name = strtolower($method->getName());

			if (
				$method->isStatic() === true ||
				str_starts_with($name, '__') === true ||
				in_array($name, $helpers, true) === true ||
				in_array($name, static::BLOCKED_FIELD_METHODS, true) === true
			) {
				continue;
			}

			$methods[] = $name;
		}

		return static::$fieldMethods = $methods;
	}

	public function toResponse()
	{
		return $this->object->toString();
	}
}
