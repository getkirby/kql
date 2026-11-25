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

	public function allowedMethods(): array
	{
		return [
			'dragText',
			'image',
			'path',
			'url',
		];
	}

	/**
	 * Only the Panel page and file have a drag text
	 */
	protected function dragText(...$args)
	{
		if (method_exists($this->object, 'dragText') === false) {
			return null;
		}

		return $this->object->dragText(...$args);
	}
}
