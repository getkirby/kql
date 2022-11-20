<?php

use Kirby\Cms\App;

@include_once __DIR__ . '/vendor/autoload.php';

require_once 'extensions/aliases.php';
require_once 'extensions/helpers.php';

App::plugin('getkirby/kql', [
	'api' => require_once 'extensions/api.php'
]);
