<?php

namespace Kirby\Kql\Interceptors\Content;

use Kirby\Kql\Interceptor;

class VersionId extends Interceptor
{
	public const CLASS_ALIAS = 'versionId';

	protected $toArray = [
		'value',
	];

	public function allowedMethods(): array
	{
		return [
			'is',
			'value'
		];
	}

	public function toResponse()
	{
		return $this->object->value();
	}
}
