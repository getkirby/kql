<?php

namespace Kirby\Kql\Interceptors\Cms;

use Kirby\Cms\Structure as StructureObjects;
use Kirby\Kql\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(StructureObject::class)]
class StructureObjectTest extends TestCase
{
	protected function item(): StructureObject
	{
		return new StructureObject(StructureObjects::factory([['name' => 'A']])->first());
	}

	public function testToArrayIncludesContent(): void
	{
		$this->assertSame(['name' => 'A', 'id' => '0'], $this->item()->toArray());
	}

	public function testToArrayLeavesOutContentListedInKqlMethodsBlocked(): void
	{
		$this->app->clone(['options' => ['kql' => ['methods' => ['blocked' => ['Kirby\Cms\StructureObject::content']]]]]);

		$this->assertSame(['id' => '0'], $this->item()->toArray());
	}
}
