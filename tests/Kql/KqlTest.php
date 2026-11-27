<?php

namespace Kirby\Kql;

use Exception;
use Kirby\Cms\Page;
use Kirby\Exception\PermissionException;
use PHPUnit\Framework\Attributes\CoversClass;
use stdClass;

#[CoversClass(Kql::class)]
class KqlTest extends TestCase
{
	public function testFetch(): void
	{
		$object = new TestObject();
		$result = Kql::fetch($object, 'more', true);
		$this->assertSame('no', $result);

		$object = new Page(['slug' => 'test']);
		$result = Kql::fetch($object, 'slug', []);
		$this->assertSame('test', $result);

		$object = new Page(['slug' => 'test']);
		$result = Kql::fetch($object, null, ['query' => 'page.slug']);
		$this->assertSame('test', $result);
	}

	public function testHelp(): void
	{
		$result = Kql::help('foo');
		$this->assertSame(['type' => 'string', 'value' => 'foo'], $result);
	}

	public function testQuery(): void
	{
		$result = Kql::run([
			'query'  => 'site.children',
			'select' => 'slug'
		]);

		$expected = [
			['slug' => 'projects'],
			['slug' => 'about'],
			['slug' => 'contact']
		];

		$this->assertSame($expected, $result);
	}

	public function testRender(): void
	{
		// non-object: returns value directly
		$result = Kql::render('foo');
		$this->assertSame('foo', $result);

		// intercepted object
		$object = new Page(['slug' => 'test']);
		$result = Kql::render($object);
		$this->assertIsArray($result);
	}

	public function testRenderArray(): void
	{
		$result = Kql::render([
			'page'  => new Page(['slug' => 'test']),
			'pages' => [new Page(['slug' => 'nested'])],
			'title' => 'Test'
		]);

		$this->assertSame('test', $result['page']['id']);
		$this->assertSame('nested', $result['pages'][0]['id']);
		$this->assertSame('Test', $result['title']);
	}

	public function testRenderArrayWithUnsupportedObject(): void
	{
		$this->expectException(PermissionException::class);
		$this->expectExceptionMessage('Access to the class "stdClass" is not supported');

		Kql::render(['object' => new stdClass()]);
	}

	public function testRenderOriginalObject(): void
	{
		$this->app->clone([
			'options' => [
				'kql' => ['classes' => ['allowed' => [
					'Kirby\Kql\TestObject',
					'Kirby\Kql\TestObjectWithMethods'
				]]]
			]
		]);

		$object = new TestObjectWithMethods();
		$result = Kql::render($object);
		$this->assertIsArray($result);

		$object = new TestObject();
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('The object "Kirby\Kql\TestObject" cannot be rendered. Try querying one of its methods instead.');
		Kql::render($object);
	}

	public function testRun(): void
	{
		$result   = Kql::run('site.title');
		$expected = 'Test Site';
		$this->assertSame($expected, $result);

		$result = Kql::run(['queries' => ['site.title']]);
		$this->assertSame([$expected], $result);

		$result = Kql::run(['query' => 'site', 'select' => 'title']);
		$this->assertSame(['title' => $expected], $result);
	}

	public function testRunArrayOfFields(): void
	{
		// field objects from `pluck()`
		$result = Kql::run('site.children.pluck("title")');
		$this->assertSame(['projects', 'about', 'contact'], $result);
	}

	public function testRunForbiddenMethod(): void
	{
		$this->expectException(PermissionException::class);
		$this->expectExceptionMessage('The method "Kirby\Cms\Page::delete()" is not allowed in the API context');
		Kql::run('site.children.first.delete');
	}

	public function testRunInterceptorMethodWithBlockedConfig(): void
	{
		$this->app->clone([
			'options' => [
				'kql' => ['methods' => ['blocked' => ['Kirby\Cms\Page::template']]]
			]
		]);

		$this->expectException(PermissionException::class);
		$this->expectExceptionMessage('The method "Kirby\Cms\Page::template()" is not allowed in the API context');

		Kql::run('site.children.first.template');
	}

	public function testRunInvalidQuery(): void
	{
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('The query must be a string');
		Kql::run(['query' => false]);
	}

	public function testRunUuid(): void
	{
		$this->app->clone([
			'options' => ['cache' => ['uuid' => false]]
		]);

		$page = new Page(['slug' => 'test', 'content' => ['uuid' => 'abc']]);
		$this->assertSame('page://abc', Kql::run('page.uuid', $page));
	}

	public function testSelect(): void
	{
		// no select, returns data via ::render
		$result = Kql::select('foo');
		$this->assertSame('foo', $result);

		// help
		$result = Kql::select('foo', '?');
		$this->assertSame(['type' => 'string', 'value' => 'foo'], $result);
	}

	public function testSelectFromArray(): void
	{
		$data = [
			'title' => 'Test Site',
			'url'   => '/'
		];

		$result = Kql::select($data, ['title' => true, 'url' => false]);
		$this->assertSame(['title' => 'Test Site'], $result);

		$result = Kql::select($data, ['title']);
		$this->assertSame(['title' => 'Test Site'], $result);

		$result = Kql::select(['page' => new Page(['slug' => 'test'])], ['page']);
		$this->assertSame('test', $result['page']['id']);
	}

	public function testSelectFromCollection(): void
	{
		$result = Kql::run([
			'select' => [
				'children' => [
					'query'      => 'site.children',
					'select'     => 'slug',
					'pagination' =>  ['limit' => 2]
				]
			]
		]);

		$this->assertCount(2, $result['children']['data']);
		$this->assertSame(2, $result['children']['pagination']['limit']);
	}

	public function testSelectFromObject(): void
	{
		$result = Kql::run([
			'select' => [
				'test' => [
					'query'  => 'site.page("about")',
					'select' => ['url' => true, 'slug' => false],
				]
			]
		]);

		$this->assertSame('/about', $result['test']['url']);
	}

	public function testSelectWithAlias(): void
	{
		$result = Kql::run([
			'select' => [
				'myTitle' => 'site.title'
			]
		]);

		$this->assertSame(['myTitle' => 'Test Site'], $result);
	}

	public function testSelectWithBoolean(): void
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

	public function testSelectWithQuery(): void
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

	public function testSelectWithString(): void
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
