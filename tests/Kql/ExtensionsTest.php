<?php

namespace Kirby\Kql;

use Kirby\Cms\App;
use Kirby\Exception\AuthException;
use Kirby\Filesystem\Dir;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversFunction;

#[CoversClass(Kql::class)]
#[CoversFunction('kql')]
class ExtensionsTest extends TestCase
{
	public const TMP = __DIR__ . '/tmp';

	protected function api(array $query, array $options = [])
	{
		$app = $this->app->clone([
			'options' => $options,
			'roots'   => [
				'sessions' => static::TMP . '/sessions'
			],
			'request' => [
				'method' => 'POST',
				'body'   => $query
			]
		]);

		return $app->api()->call('query', 'POST');
	}

	public function tearDown(): void
	{
		if (is_dir(static::TMP) === true) {
			App::instance()->session()->destroy();
			Dir::remove(static::TMP);
		}
	}

	public function testApiRoute(): void
	{
		$result = $this->api(
			query: ['query' => 'site', 'select' => ['title' => true]],
			options: ['kql' => ['auth' => false]]
		);

		$this->assertSame([
			'code'   => 200,
			'result' => ['title' => 'Test Site'],
			'status' => 'ok',
		], $result);
	}

	public function testApiRouteRequiresAuthentication(): void
	{
		$this->expectException(AuthException::class);
		$this->expectExceptionMessage('Unauthenticated');
		$this->api(query: ['query' => 'site.title']);
	}

	public function testHelper(): void
	{
		$this->assertSame('Test Site', kql('site.title'));
		$this->assertSame('about', kql('page.slug', $this->app->page('about')));
		$this->assertSame(['title' => 'Test Site'], kql(['select' => 'title']));
	}
}
