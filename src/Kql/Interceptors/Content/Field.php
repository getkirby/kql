<?php

namespace Kirby\Kql\Interceptors\Content;

use Kirby\Kql\Interceptor;

class Field extends Interceptor
{
	public const CLASS_ALIAS = 'field';

	public function __call($method, array $args = [])
	{
		// the block list from the config also covers
		// core field methods and custom field methods
		if ($this->isBlockedMethod($method) === true) {
			$this->forbiddenMethod($method);
		}

		if ($this->isAllowedMethod($method) === true) {
			return $this->callMethod($method, $args);
		}

		// field methods
		$methods = array_keys($this->object::$methods);
		$method  = strtolower($method);

		if (in_array($method, $methods) === true) {
			return $this->object->$method(...$args);
		}

		// aliases
		$aliases = array_keys($this->object::$aliases);
		$alias   = strtolower($method);

		if (in_array($alias, $aliases) === true) {
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

	protected function isBlockedMethod(string $method): bool
	{
		// a custom field method can take over an alias name
		$aliasedMethod = isset($this->object::$methods[strtolower($method)]) === false
			? $this->object::$aliases[strtolower($method)] ?? null
			: null;

		if ($aliasedMethod !== null && parent::isBlockedMethod($aliasedMethod) === true) {
			return true;
		}

		return parent::isBlockedMethod($method);
	}

	public function toResponse()
	{
		return $this->object->toString();
	}
}
