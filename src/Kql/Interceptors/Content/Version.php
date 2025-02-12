<?php

namespace Kirby\Kql\Interceptors\Content;

use Kirby\Kql\Interceptor;

class Version extends Interceptor
{
	public const CLASS_ALIAS = 'version';

	protected $toArray = [
		'content',
		'id',
		'exists',
		'isLatest',
		'lock',
		'modified',
	];

	public function allowedMethods(): array
	{
		return [
			'content',
			'id',
			'exists',
			'isLatest',
			'isLocked',
			'lock',
			'modified',
			'read',
		];
	}
}
