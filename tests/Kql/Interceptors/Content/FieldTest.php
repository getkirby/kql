<?php

namespace Kirby\Kql\Interceptors\Content;

use Kirby\Cms\Blocks;
use Kirby\Content\Field as FieldObject;
use Kirby\Exception\PermissionException;
use Kirby\Kql\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(Field::class)]
class FieldTest extends TestCase
{
	public function tearDown(): void
	{
		FieldObject::$methods = [];
	}

	public static function blockedMethodProvider(): array
	{
		return [
			['getMethod'],
			['hasMethod'],
			['kirby'],
			['model'],
			['parent'],
			['toEnum'],
		];
	}

	#[DataProvider('blockedMethodProvider')]
	public function testBlockedMethod(string $method): void
	{
		$field = new Field(new FieldObject(null, 'test', 'a'));

		$this->expectException(PermissionException::class);
		$this->expectExceptionMessage('The method "Kirby\Content\Field::' . $method . '()" is not allowed in the API context');

		$field->$method('test');
	}

	public function testCoreMethods(): void
	{
		$field = new Field(new FieldObject(null, 'test', 'Hello World'));

		$this->assertSame('HELLO WORLD', $field->upper()->value());
		$this->assertSame('HELLO WORLD', $field->UPPER()->value());
		$this->assertSame('hello-world', $field->slug()->value());
		$this->assertSame(2, $field->words());
		$this->assertInstanceOf(Blocks::class, $field->toBlocks());
	}

	public function testCustomMethod(): void
	{
		$this->app->clone([
			'fieldMethods' => [
				'shout' => fn (FieldObject $field) => $field->value(strtoupper($field->value) . '!')
			]
		]);

		$field = new Field(new FieldObject(null, 'test', 'hello'));
		$this->assertSame('HELLO!', $field->shout()->value());
	}

	public function testFieldMethods(): void
	{
		$methods = Field::fieldMethods();

		$this->assertContains('kirbytext', $methods);
		$this->assertContains('toblocks', $methods);
		$this->assertContains('upper', $methods);
		$this->assertNotContains('__call', $methods);
		$this->assertNotContains('getmethod', $methods);
		$this->assertNotContains('hasmethod', $methods);
		$this->assertNotContains('toenum', $methods);
	}

	public function testToResponse(): void
	{
		$field = new Field(new FieldObject(null, 'test', 'hello'));
		$this->assertSame('hello', $field->toResponse());
	}
}
