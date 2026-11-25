<?php

namespace Kirby\Kql\Interceptors\Panel;

use Kirby\Exception\PermissionException;
use Kirby\Kql\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Model::class)]
class ModelTest extends TestCase
{
	public function testDragTextReturnsNullForSite(): void
	{
		$this->assertNull((new Model($this->app->site()->panel()))->dragText());
	}

	public function testDragTextReturnsPageLink(): void
	{
		$app   = $this->app->clone(['options' => ['content' => ['uuid' => false]]]);
		$panel = new Model($app->page('projects')->panel());

		$this->assertSame('(link: projects text: projects)', $panel->dragText());
	}

	public function testRefusesDragTextListedInKqlMethodsBlocked(): void
	{
		$app = $this->app->clone(['options' => ['kql' => ['methods' => ['blocked' => ['Kirby\Panel\Site::dragText']]]]]);

		$this->expectException(PermissionException::class);
		(new Model($app->site()->panel()))->dragText();
	}

	public function testToArrayLeavesOutDragTextListedInKqlMethodsBlocked(): void
	{
		$app = $this->app->clone(['options' => ['kql' => ['methods' => ['blocked' => ['Kirby\Panel\Site::dragText']]]]]);

		$this->assertArrayNotHasKey('dragText', (new Model($app->site()->panel()))->toArray());
	}
}
