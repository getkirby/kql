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
	public function testFor()
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
	public function testForArray()
	{
		$result = Help::for(['foo' => 'bar', 'kirby' => 'cms']);
		$this->assertSame(['type' => 'array', 'keys' => ['foo', 'kirby']], $result);
	}

	/**
	 * @covers ::forMethod
	 */
	public function testForMethod()
	{
		$object = new TestObject();
		$result = Help::forMethod($object, 'foo');
		$this->assertSame([
			'call'   => '.foo(string $bar)',
			'name'   => 'foo',
			'params' => [
				'bar' => [
					'name' => 'bar',
					'type' => 'string',
					'required' => false,
					'default' => 'hello',
					'call' => 'string $bar'
				]
			],
			'returns' => 'array'
		], $result);
	}

	/**
	 * @covers ::forMethods
	 */
	public function testForMethods()
	{
		$object = new TestObject();
		$result = Help::forMethods($object, ['more', 'foo', 'more']);
		$this->assertSame([
			'foo' => [
				'call'   => '.foo(string $bar)',
				'name'   => 'foo',
				'params' => [
					'bar' => [
						'name' => 'bar',
						'type' => 'string',
						'required' => false,
						'default' => 'hello',
						'call' => 'string $bar'
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
	public function testForObjectWithInterceptedObject()
	{
		$object = new Page(['slug' => 'test']);
		$result = Help::forObject($object);

		$this->assertSame('page', $result['type']);
		$this->assertArrayHasKey('methods', $result);
		$this->assertArrayHasKey('value', $result);
	}

	/**
	 * @covers ::for
	 * @covers ::forObject
	 */
	public function testForObjectWithOriginalObject()
	{
		$this->app->clone([
			'options' => [
				'kql' => ['classes' => ['allowed' => ['Kirby\Kql\TestObject']]]
			]
		]);

		$object = new TestObject();
		$result = Help::forObject($object);

		$this->assertSame('Kirby\Kql\TestObject', $result['type']);
		$this->assertCount(2, $result['methods']);
		$this->assertSame('.foo(string $bar)', $result['methods'][0]['call']);
	}
}
