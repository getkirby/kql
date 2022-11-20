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

	public function allowedMethods(): array
	{
		return [
			'more'
		];
	}
}


/**
 * @coversDefaultClass \Kirby\Kql\Interceptor
 */
class InterceptorTest extends TestCase
{
	/**
	 * @covers ::__construct
	 * @covers ::__call
	 */
	public function testCall()
	{
		$object      = new TestObject();
		$interceptor = new TestInterceptor($object);
		$this->assertSame('no', $interceptor->more());

		$this->expectException(PermissionException::class);
		$this->expectExceptionMessage('The method "Kirby\Kql\TestObject::foo()" is not allowed in the API context');
		$interceptor->foo('test');
	}

	/**
	 * @covers ::__debugInfo
	 */
	public function testDebugInfo()
	{
		$object      = new TestObject();
		$interceptor = new TestInterceptor($object);
		$result      = $interceptor->__debugInfo();

		$this->assertSame('test', $result['type']);
		$this->assertArrayHasKey('methods', $result);
		$this->assertArrayHasKey('value', $result);
	}

	/**
	 * @covers ::allowedMethods
	 */
	public function testAllowedMethods()
	{
		$object      = new TestObject();
		$interceptor = new TestInterceptor($object);
		$this->assertSame(['more'], $interceptor->allowedMethods());
	}

	/**
	 * @covers ::class
	 */
	public function testClass()
	{
		$this->assertSame('Kirby\Kql\Interceptors\Kql\Test', Interceptor::class('Kirby\Kql\Test'));
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

	/**
	 * @covers ::replace
	 * @dataProvider objectProvider
	 */
	public function testReplace($object, $inspector)
	{
		$result = Interceptor::replace($object);
		$this->assertInstanceOf($inspector, $result);
	}

	/**
	 * @covers ::replace
	 */
	public function testReplaceNonObject()
	{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('Unsupported value: string');

		$result = Interceptor::replace('hello');
	}

	/**
	 * @covers ::replace
	 */
	public function testReplaceUnknownObject()
	{
		$this->expectException(PermissionException::class);
		$this->expectExceptionMessage('Access to the class "stdClass" is not supported');

		$object = new \stdClass();
		$result = Interceptor::replace($object);
	}
}
