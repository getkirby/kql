<?php

namespace Kirby\Kql;

use Kirby\Cms\App;

error_reporting(E_ALL);

define('KIRBY_HELPER_DUMP', false);

ini_set('memory_limit', '512M');
ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../index.php';

// don't register any error handlers in tests
App::$enableWhoops = false;
