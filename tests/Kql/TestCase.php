<?php

namespace Kirby\Kql;

use Kirby\Cms\App;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestObject
{
	public function foo(string $bar = 'hello'): array
	{
		return [$bar];
	}

	public function more(): string
	{
		return 'no';
	}
}

class TestCase extends BaseTestCase
{
	protected App $app;

	public function setUp(): void
	{
		$this->app = new App([
			'roots' => [
				'index' => '/dev/null'
			],
			'site' => [
				'children' => [
					[
						'slug' => 'projects'
					],
					[
						'slug' => 'about'
					],
					[
						'slug' => 'contact'
					]
				],
				'content' => [
					'title' => 'Test Site'
				],
			]
		]);
	}
}
