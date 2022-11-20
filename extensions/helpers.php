<?php

use Kirby\Kql\Kql;

function kql($input, $model = null)
{
	return Kql::run($input, $model);
}
