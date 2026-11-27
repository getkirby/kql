<?php

namespace Kirby\Kql\Interceptors\Cms;

use Kirby\Kql\Interceptor;

class Translation extends Interceptor
{
	public const CLASS_ALIAS = 'translation';

	// `data` holds every Panel string of the language,
	// so it is only returned when a query asks for it
	protected $toArray = [
		'code',
		'direction',
		'id',
		'name',
		'locale',
		'author'
	];

	public function allowedMethods(): array
	{
		return [
			'code',
			'data',
			'dataWithFallback',
			'direction',
			'get',
			'id',
			'name',
			'locale',
			'author'
		];
	}
}
