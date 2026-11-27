<?php

namespace Kirby\Kql\Interceptors\Cms;

use Kirby\Kql\Interceptor;
use Kirby\Toolkit\Locale;

class Language extends Interceptor
{
	public const CLASS_ALIAS = 'language';

	// `rules` holds every slug rule of the language,
	// so it is only returned when a query asks for it
	protected $toArray = [
		'code',
		'direction',
		'isDefault',
		'locale',
		'name',
		'url'
	];

	public function allowedMethods(): array
	{
		return [
			'code',
			'direction',
			'id',
			'isDefault',
			'locale',
			'name',
			'rules',
			'url'
		];
	}

	/**
	 * Names the locale categories, as the values of
	 * the `LC_*` constants differ between systems
	 */
	protected function locale()
	{
		return Locale::export($this->object->locale());
	}
}
