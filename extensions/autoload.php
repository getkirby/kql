<?php

namespace Kirby\Kql;

use Kirby\Toolkit\Str;

function autoload(string $namespace, string $dir)
{
	spl_autoload_register(function ($class) use ($namespace, $dir) {
		if (str_contains($class, '.') === true || str_starts_with($class, $namespace) === false) {
			return;
		}

		$path = Str::after($class, $namespace);
		$path = $dir . '/' . str_replace('\\', '/', $path) . '.php';

		if (is_file($path) === true) {
			include $path;
		}
	});
}
