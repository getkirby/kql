<?php

use Kirby\Cms\Helpers;
use Kirby\Kql\Kql;

if (Helpers::hasOverride('kql') === false) {
	function kql($input, $model = null)
	{
		return Kql::run($input, $model);
	}
}
