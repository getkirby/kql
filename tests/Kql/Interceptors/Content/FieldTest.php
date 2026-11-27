<?php

namespace Kirby\Kql\Interceptors\Content;

use Kirby\Cms\Field as FieldObject;
use Kirby\Exception\PermissionException;
use Kirby\Kql\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(Field::class)]
class FieldTest extends TestCase
{
	protected array $methods;

	public function setUp(): void
	{
		parent::setUp();
		$this->methods = FieldObject::$methods;
	}

	public function tearDown(): void
	{
		FieldObject::$methods = $this->methods;
	}

	public static function blockedConfigMethodProvider(): array
	{
		return [
			['toPages'],
			['TOPAGES'],
			['shout'],
		];
	}

	#[DataProvider('blockedConfigMethodProvider')]
	public function testBlockedMethodWithBlockedConfig(string $method): void
	{
		$object = new FieldObject(null, 'test', 'a');

		$this->app->clone([
			'fieldMethods' => [
				'shout' => fn (FieldObject $field) => $field
			],
			'options' => [
				'kql' => ['methods' => ['blocked' => [
					$object::class . '::toPages',
					$object::class . '::shout'
				]]]
			]
		]);

		$this->expectException(PermissionException::class);
		$this->expectExceptionMessage('The method "' . $object::class . '::' . $method . '()" is not allowed in the API context');

		(new Field($object))->$method();
	}

	public function testBlockedMethodWithBlockedConfigForAlias(): void
	{
		$object = new FieldObject(null, 'test', 'a');

		$this->app->clone([
			'options' => [
				'kql' => ['methods' => ['blocked' => [$object::class . '::kirbytext']]]
			]
		]);

		$this->expectException(PermissionException::class);
		$this->expectExceptionMessage('The method "' . $object::class . '::kt()" is not allowed in the API context');

		(new Field($object))->kt();
	}

	public function testCustomMethodWithAliasName(): void
	{
		$object = new FieldObject(null, 'test', 'a');

		$this->app->clone([
			'fieldMethods' => [
				'kt' => fn (FieldObject $field) => 'custom'
			],
			'options' => [
				'kql' => ['methods' => ['blocked' => [$object::class . '::kirbytext']]]
			]
		]);

		$this->assertSame('custom', (new Field($object))->kt());
	}
}
