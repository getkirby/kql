<?php

namespace Kirby\Kql;

use Kirby\Exception\PermissionException;

/**
 * @coversDefaultClass \Kirby\Kql\Kql
 */
class KqlTest extends TestCase
{

	/**
	 * @covers ::forbiddenMethod
	 */
	public function testForbiddenMethod()
	{
		$this->expectException(PermissionException::class);
		$this->expectExceptionMessage('The method "Kirby\Cms\Page::delete()" is not allowed in the API context');
		Kql::run('site.children.first.delete');
	}

	/**
	 * @covers ::query
	 */
	public function testQuery()
	{
		$result = Kql::run([
			'query'  => 'site.children',
			'select' => 'slug'
		]);

		$expected = [
			[
				'slug' => 'projects',
			],
			[
				'slug' => 'about',
			],
			[
				'slug' => 'contact',
			]
		];

		$this->assertSame($expected, $result);
	}

	/**
	 * @covers ::run
	 */
	public function testRun()
	{
		$result   = Kql::run('site.title');
		$expected = 'Test Site';

		$this->assertSame($expected, $result);
	}

	/**
	 * @covers ::select
	 */
	public function testSelectWithAlias()
	{
		$result = Kql::run([
			'select' => [
				'myTitle' => 'site.title'
			]
		]);

		$expected = [
			'myTitle' => 'Test Site',
		];

		$this->assertSame($expected, $result);
	}

	/**
	 * @covers ::select
	 * @covers ::selectFromArray
	 */
	public function testSelectWithArray()
	{
		$result = Kql::run([
			'select' => ['title', 'url']
		]);

		$expected = [
			'title' => 'Test Site',
			'url'   => '/'
		];

		$this->assertSame($expected, $result);
	}

	/**
	 * @covers ::select
	 */
	public function testSelectWithBoolean()
	{
		$result = Kql::run([
			'select' => [
				'title' => true
			]
		]);

		$expected = [
			'title' => 'Test Site'
		];

		$this->assertSame($expected, $result);
	}

	/**
	 * @covers ::select
	 * @covers ::selectFromCollection
	 * @covers ::selectFromObject
	 */
	public function testSelectWithQuery()
	{
		$result = Kql::run([
			'select' => [
				'children' => [
					'query'  => 'site.children',
					'select' => 'slug'
				]
			]
		]);

		$expected = [
			'children' => [
				[
					'slug' => 'projects',
				],
				[
					'slug' => 'about',
				],
				[
					'slug' => 'contact',
				]
			]
		];

		$this->assertSame($expected, $result);
	}

	/**
	 * @covers ::select
	 */
	public function testSelectWithString()
	{
		$result = Kql::run([
			'select' => [
				'title' => 'site.title.upper'
			]
		]);

		$expected = [
			'title' => 'TEST SITE'
		];

		$this->assertSame($expected, $result);
	}
}
