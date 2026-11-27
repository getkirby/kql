<?php

namespace Kirby\Kql\Interceptors;

use Generator;
use Kirby\Filesystem\Dir;
use Kirby\Kql\TestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;

#[CoversNothing]
class AllowedMethodsTest extends TestCase
{
	/**
	 * Kirby classes that hand unknown names
	 * to the methods of their asset
	 */
	protected const ASSET_CLASSES = [
		'Kirby\Cms\File'        => ['Kirby\Filesystem\File', 'Kirby\Image\Image'],
		'Kirby\Cms\FileVersion' => ['Kirby\Filesystem\File', 'Kirby\Image\Image'],
	];

	/**
	 * Names answered by a subclass or `__call()`
	 */
	protected const UNDECLARED_NAMES = [
		'Kirby\Blueprint\Blueprint' => ['description', 'options'],
		'Kirby\Cms\FileVersion'     => ['template'],
		'Kirby\Cms\Site'            => ['title'],
		'Kirby\Panel\Model'         => ['dragText'],
	];

	public static function interceptorProvider(): Generator
	{
		$root = dirname(__DIR__, 3) . '/src/Kql/Interceptors';

		foreach (Dir::index($root, true) as $path) {
			if (str_ends_with($path, '.php') === false) {
				continue;
			}

			$relativeClass = str_replace('/', '\\', substr($path, 0, -4));
			$reflection    = new ReflectionClass('Kirby\Kql\Interceptors\\' . $relativeClass);

			if ($reflection->isAbstract() === false) {
				yield [$relativeClass];
			}
		}
	}

	/**
	 * On models, an allowed name without a method
	 * falls through to a content field without notice
	 */
	#[DataProvider('interceptorProvider')]
	public function testAllowedNamesExistInKirby(string $relativeClass): void
	{
		$reflection = new ReflectionClass('Kirby\Kql\Interceptors\\' . $relativeClass);
		$target     = 'Kirby\\' . $relativeClass;

		$this->assertTrue(class_exists($target), $target . ' does not exist');

		$interceptor = $reflection->newInstanceWithoutConstructor();
		$names       = [
			...$interceptor->allowedMethods(),
			...$reflection->getProperty('toArray')->getValue($interceptor)
		];

		$missing = array_filter(
			array_unique($names),
			function (string $method) use ($target) {
				if (in_array($method, static::UNDECLARED_NAMES[$target] ?? [], true) === true) {
					return false;
				}

				foreach ([$target, ...static::ASSET_CLASSES[$target] ?? []] as $class) {
					if (method_exists($class, $method) === true) {
						return false;
					}
				}

				return true;
			}
		);

		$this->assertSame([], array_values($missing));
	}
}
