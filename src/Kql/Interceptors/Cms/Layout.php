<?php

namespace Kirby\Kql\Interceptors\Cms;

class Layout extends Model
{
	public const CLASS_ALIAS = 'layout';

	protected $toArray = [
		'attrs',
		'columns',
		'id',
		'isEmpty',
	];

	public function __call($method, array $args = [])
	{
		if ($this->isAllowedMethod($method) === true) {
			return $this->object->$method(...$args);
		}

		// the attribute fallback of `Kirby\Cms\Layout::__call()`
		if (method_exists($this->object, $method) === false) {
			return $this->object->attrs()->get($method);
		}

		$this->forbiddenMethod($method);
	}

	public function allowedMethods(): array
	{
		return array_merge(
			$this->allowedMethodsForSiblings(),
			[
				'attrs',
				'columns',
				'id',
				'isEmpty',
				'isNotEmpty',
				'parent'
			]
		);
	}
}
