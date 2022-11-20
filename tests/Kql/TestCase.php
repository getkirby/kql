<?php

namespace Kirby\Kql;

use Kirby\Cms\App;
use PHPUnit\Framework\TestCase as BaseTestCase;

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
