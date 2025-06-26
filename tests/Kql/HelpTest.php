<?php

namespace Kirby\Kql;

use Kirby\Cms\Page;

/**
 * @coversDefaultClass \Kirby\Kql\Help
 */
class HelpTest extends TestCase
{
	/**
	 * @covers ::for
	 */
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

	/**
	 * @covers ::for
	 * @covers ::forArray
	 */
	public function testForArray(): void
	{
		$result = Help::for(['foo' => 'bar', 'kirby' => 'cms']);
		$this->assertSame(['type' => 'array', 'keys' => ['foo', 'kirby']], $result);
	}

	/**
	 * @covers ::forMethod
	 */
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

	/**
	 * @covers ::forMethods
	 */
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

	/**
	 * @covers ::for
	 * @covers ::forObject
	 */
	public function testForObjectWithInterceptedObject(): void
	{
		$object = new Page(['slug' => 'test']);
		$result = Help::for($object);

		$this->assertSame('page', $result['type']);
		$this->assertArrayHasKey('methods', $result);
		$this->assertArrayHasKey('value', $result);
	}

	/**
	 * @covers ::for
	 * @covers ::forObject
	 */
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
