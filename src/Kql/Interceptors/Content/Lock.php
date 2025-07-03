<?php

namespace Kirby\Kql\Interceptors\Content;

use Kirby\Kql\Interceptor;

class Lock extends Interceptor
{
	public const CLASS_ALIAS = 'lock';

	public function allowedMethods(): array
	{
		return [
			'isActive',
			'isLocked',
			'modified',
			'toArray',
			'user',
		];
	}

	public function toArray(): array
	{
		return $this->object->toArray();
	}
}
