<?php

namespace Kirby\Kql;

use Kirby\Cms\App;

require_once __DIR__ . '/extensions/autoload.php';

autoload('Kirby\\', __DIR__ . '/src/');

require_once __DIR__ . '/extensions/aliases.php';
require_once __DIR__ . '/extensions/helpers.php';

App::plugin('getkirby/kql', [
	'api' => require_once 'extensions/api.php'
]);
