<?php

namespace Kirby\Kql;

use Kirby\Cms\App;
use Kirby\Cms\Blueprint;
use Kirby\Cms\Content;
use Kirby\Cms\Field;
use Kirby\Cms\File;
use Kirby\Cms\FileBlueprint;
use Kirby\Cms\FileVersion;
use Kirby\Cms\Page;
use Kirby\Cms\PageBlueprint;
use Kirby\Cms\Role;
use Kirby\Cms\Site;
use Kirby\Cms\SiteBlueprint;
use Kirby\Cms\User;
use Kirby\Cms\UserBlueprint;
use Kirby\Exception\InvalidArgumentException;
use Kirby\Exception\PermissionException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;

class AppExtended extends App
{
}
class FileExtended extends File
{
}
class PageExtended extends Page
{
}
class RoleExtended extends User
{
}
class SiteExtended extends Site
{
}
class UserExtended extends User
{
}

class TestInterceptor extends Interceptor
{
	public const CLASS_ALIAS = 'test';

	protected $toArray = ['more', 'foo'];

	public function allowedMethods(): array
	{
		return [
			'more'
		];
	}
}


#[CoversClass(\Kirby\Kql\Interceptor::class)]
class InterceptorTest extends TestCase
{
	public function testCall(): void
	{
		$object      = new TestObject();
		$interceptor = new TestInterceptor($object);
		$this->assertSame('no', $interceptor->more());

		$this->expectException(PermissionException::class);
		$this->expectExceptionMessage('The method "Kirby\Kql\TestObject::foo()" is not allowed in the API context');
		$interceptor->foo('test');
	}

	public function testDebugInfo(): void
	{
		$object      = new TestObject();
		$interceptor = new TestInterceptor($object);
		$result      = $interceptor->__debugInfo();

		$this->assertSame('test', $result['type']);
		$this->assertArrayHasKey('methods', $result);
		$this->assertArrayHasKey('value', $result);
	}

	public function testAllowedMethods(): void
	{
		$object      = new TestObject();
		$interceptor = new TestInterceptor($object);
		$this->assertSame(['more'], $interceptor->allowedMethods());
	}

	public function testClass(): void
	{
		$this->assertSame('Kirby\Kql\Interceptors\Kql\Test', Interceptor::class('Kirby\Kql\Test'));
	}

	public function testIsAllowedMethod(): void
	{
		$object      = new TestObject();
		$interceptor = new TestInterceptor($object);
		$this->assertTrue($interceptor->isAllowedMethod('more'));
		$this->assertFalse($interceptor->isAllowedMethod('foo'));
	}

	public function testIsAllowedMethodWithBlockedConfig(): void
	{
		$this->app->clone([
			'options' => [
				'kql' => ['methods' => ['blocked' => ['Kirby\Kql\TestObject::more']]]
			]
		]);

		$object      = new TestObject();
		$interceptor = new TestInterceptor($object);
		$this->assertFalse($interceptor->isAllowedMethod('more'));
	}

	public function testIsAllowedMethodWithAllowedConfig(): void
	{
		$this->app->clone([
			'options' => [
				'kql' => ['methods' => ['allowed' => ['Kirby\Kql\TestObject::foo']]]
			]
		]);

		$object      = new TestObject();
		$interceptor = new TestInterceptor($object);
		$this->assertTrue($interceptor->isAllowedMethod('foo'));
	}

	public function testIsAllowedCallable(): void
	{
		$object      = new TestObject();
		$interceptor = new TestInterceptor($object);
		$this->assertTrue($interceptor->isAllowedMethod('homer'));
	}

	public function testIsAllowedCustomMethod(): void
	{
		$object      = new TestObject();
		$interceptor = new TestInterceptor($object);
		$this->assertFalse($interceptor->isAllowedMethod('simple'));

		$object      = new TestObjectWithMethods();
		$interceptor = new TestInterceptor($object);
		$this->assertFalse($interceptor->isAllowedMethod('simple'));

		TestObjectWithMethods::$methods = ['simple' => fn () => false];
		$this->assertFalse($interceptor->isAllowedMethod('simple'));

		$object      = new TestObjectWithMethodsAsChild();
		$interceptor = new TestInterceptor($object);
		$this->assertFalse($interceptor->isAllowedMethod('simple'));

		TestObjectWithMethods::$methods = ['closure' => function () {
		}];
		$this->assertFalse($interceptor->isAllowedMethod('closure'));

		TestObjectWithMethods::$methods = [
			/**
			 * @kql-allowed
			 */
			'closure' => function () {
			}
		];
		$this->assertTrue($interceptor->isAllowedMethod('closure'));

		TestObjectWithMethods::$methods = ['invalid' => 5];
		$this->assertFalse($interceptor->isAllowedMethod('invalid'));
	}

	public function objectProvider()
	{
		return [
			[
				new App(),
				'Kirby\Kql\Interceptors\Cms\App'
			],
			[
				new AppExtended(),
				'Kirby\Kql\Interceptors\Cms\App'
			],
			[
				new Blueprint([
					'model' => $page = new Page([
						'slug' => 'test'
					]),
					'name'  => 'test',
				]),
				'Kirby\Kql\Interceptors\Cms\Blueprint'
			],
			[
				new Content(),
				'Kirby\Kql\Interceptors\Cms\Content'
			],
			[
				new Field(null, 'key', 'value'),
				'Kirby\Kql\Interceptors\Cms\Field'
			],
			[
				$file = new File(['filename' => 'test.jpg', 'parent' => $page]),
				'Kirby\Kql\Interceptors\Cms\File'
			],
			[
				new FileBlueprint([
					'model' => $file,
					'name' => 'test',
				]),
				'Kirby\Kql\Interceptors\Cms\Blueprint'
			],
			[
				new FileExtended(['filename' => 'test.jpg', 'parent' => $page]),
				'Kirby\Kql\Interceptors\Cms\File'
			],
			[
				new FileVersion([
					'original' => $file,
					'url' => '/test.jpg'
				]),
				'Kirby\Kql\Interceptors\Cms\FileVersion'
			],
			[
				$page,
				'Kirby\Kql\Interceptors\Cms\Page'
			],
			[
				new PageBlueprint([
					'model' => $page,
					'name'  => 'test',
				]),
				'Kirby\Kql\Interceptors\Cms\Blueprint'
			],
			[
				new PageExtended(['slug' => 'test']),
				'Kirby\Kql\Interceptors\Cms\Page'
			],
			[
				new Role(['name' => 'admin']),
				'Kirby\Kql\Interceptors\Cms\Role'
			],
			[
				new Site(),
				'Kirby\Kql\Interceptors\Cms\Site'
			],
			[
				new SiteBlueprint([
					'model' => new Site(),
					'name'  => 'test',
				]),
				'Kirby\Kql\Interceptors\Cms\Blueprint'
			],
			[
				new SiteExtended(),
				'Kirby\Kql\Interceptors\Cms\Site'
			],
			[
				$user = new User(['email' => 'test@getkirby.com']),
				'Kirby\Kql\Interceptors\Cms\User'
			],
			[
				new UserBlueprint([
					'model' => $user,
					'name'  => 'test',
				]),
				'Kirby\Kql\Interceptors\Cms\Blueprint'
			],
			[
				new UserExtended(['email' => 'test@getkirby.com']),
				'Kirby\Kql\Interceptors\Cms\User'
			]
		];
	}

	#[DataProvider('objectProvider')]
	public function testReplace($object, $interceptor): void
	{
		$result = Interceptor::replace($object);
		$this->assertInstanceOf($interceptor, $result);
	}

	public function testReplaceNonObject(): void
	{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('Unsupported value: string');
		Interceptor::replace('hello');
	}

	public function testReplaceBlockedOptions(): void
	{
		$this->app->clone([
			'options' => [
				'kql' => ['classes' => ['blocked' => ['Kirby\Kql\TestOBJect']]]
			]
		]);

		$this->expectException(PermissionException::class);
		$this->expectExceptionMessage('Access to the class "Kirby\Kql\TestObject" is blocked');
		Interceptor::replace(new TestObject());
	}

	public function testReplaceObjectIsInterceptor(): void
	{
		$object      = new TestObject();
		$interceptor = new TestInterceptor($object);
		$this->assertSame($interceptor, Interceptor::replace($interceptor));
	}

	public function testReplaceAllowedOptions(): void
	{
		$this->app->clone([
			'options' => [
				'kql' => ['classes' => ['allowed' => ['stdClass']]]
			]
		]);

		$object = new stdClass();
		$this->assertSame($object, Interceptor::replace($object));
	}

	public function testReplaceUnknownObject(): void
	{
		$this->expectException(PermissionException::class);
		$this->expectExceptionMessage('Access to the class "stdClass" is not supported');
		$object = new stdClass();
		Interceptor::replace($object);
	}

	public function testToArray(): void
	{
		$object      = new TestObject();
		$interceptor = new TestInterceptor($object);
		$expected    = ['more' => 'no'];
		$this->assertSame($expected, $interceptor->toArray());
		$this->assertSame($expected, $interceptor->toResponse());
	}
}
