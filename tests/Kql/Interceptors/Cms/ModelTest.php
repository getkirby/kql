<?php

namespace Kirby\Kql\Interceptors\Cms;

use Kirby\Kql\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Model::class)]
class ModelTest extends TestCase
{
	public function testUuidReturnsNullWithContentUuidFalse(): void
	{
		$app = $this->app->clone(['options' => ['content' => ['uuid' => false]]]);

		$this->assertNull((new Site($app->site()))->uuid());
	}
}
