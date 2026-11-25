<?php

namespace Kirby\Kql\Interceptors\Panel;

use Kirby\Kql\Interceptor;

class Model extends Interceptor
{
	public const CLASS_ALIAS = 'panel';

	protected $toArray = [
		'dragText',
		'image',
		'path',
		'url',
	];

	public function __call(string $method, array $args = [])
	{
		// only the Panel page and file have a drag text
		if (
			strtolower($method) === 'dragtext' &&
			$this->isAllowedMethod($method) === true &&
			method_exists($this->object, 'dragText') === false
		) {
			return null;
		}

		return parent::__call($method, $args);
	}

	public function allowedMethods(): array
	{
		return [
			'dragText',
			'image',
			'path',
			'url',
		];
	}
}
