<?php

namespace Kirby\Kql\Interceptors\Cms;

use Kirby\Kql\Interceptor;

class LayoutColumn extends Model
{
	public const CLASS_ALIAS = 'layoutColumn';

	protected $toArray = [
		'blocks',
		'id',
		'isEmpty',
		'width',
	];

	/**
	 * A column has no content to fall back to
	 */
	public function __call($method, array $args = [])
	{
		return Interceptor::__call($method, $args);
	}

	public function allowedMethods(): array
	{
		return array_merge(
			$this->allowedMethodsForSiblings(),
			[
				'blocks',
				'id',
				'isEmpty',
				'isNotEmpty',
				'span',
				'width'
			]
		);
	}
}
