<?php

namespace Kirby\Kql;

use Kirby\Cms\Page;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Kirby\Kql\Help::class)]
class HelpTest extends TestCase
{
	public function testFor(): void
	{
		$result = Help::for('foo');
		$this->assertSame(['type' => 'string', 'value' => 'foo'], $result);

		$result = Help::for(3);
		$this->assertSame(['type' => 'integer', 'value' => 3], $result);

		$result = Help::for(3.0);
		$this->assertSame(['type' => 'double', 'value' => 3.0], $result);

		$result = Help::for(false);
		$this->assertSame(['type' => 'boolean', 'value' => false], $result);
	}

	public function testForArray(): void
	{
		$result = Help::for(['foo' => 'bar', 'kirby' => 'cms']);
		$this->assertSame(['type' => 'array', 'keys' => ['foo', 'kirby']], $result);
	}

	public function testForMethod(): void
	{
		$object = new TestObject();
		$result = Help::forMethod($object, 'foo');
		$this->assertSame([
			'call'   => '.foo(string $bar = \'hello\')',
			'name'   => 'foo',
			'params' => [
				'bar' => [
					'name' => 'bar',
					'type' => 'string',
					'required' => false,
					'default' => 'hello',
					'call' => 'string $bar = \'hello\''
				]
			],
			'returns' => 'array'
		], $result);
	}

	public function testForMethods(): void
	{
		$object = new TestObject();
		$result = Help::forMethods($object, ['more', 'foo', 'more', '404']);
		$this->assertSame([
			'foo' => [
				'call'   => '.foo(string $bar = \'hello\')',
				'name'   => 'foo',
				'params' => [
					'bar' => [
						'name' => 'bar',
						'type' => 'string',
						'required' => false,
						'default' => 'hello',
						'call' => 'string $bar = \'hello\''
					]
				],
				'returns' => 'array'
			],
			'more' => [
				'call'   => '.more',
				'name'   => 'more',
				'params' => [],
				'returns' => 'string'
			]
		], $result);
	}

	public function testForObjectWithInterceptedObject(): void
	{
		$object = new Page(['slug' => 'test']);
		$result = Help::for($object);

		$this->assertSame('page', $result['type']);
		$this->assertArrayHasKey('methods', $result);
		$this->assertArrayHasKey('value', $result);
	}

	public function testForObjectWithOriginalObject(): void
	{
		$this->app->clone([
			'options' => [
				'kql' => ['classes' => ['allowed' => ['Kirby\Kql\TestObject']]]
			]
		]);

		$object = new TestObject();
		$result = Help::for($object);

		$this->assertSame('Kirby\Kql\TestObject', $result['type']);
		$this->assertCount(3, $result['methods']);
		$this->assertSame('.foo(string $bar = \'hello\')', $result['methods'][0]['call']);
	}
}
