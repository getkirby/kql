<?php

namespace Kirby\Kql\Interceptors\Cms;

use Kirby\Cms\Layout as LayoutObject;
use Kirby\Exception\PermissionException;
use Kirby\Kql\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Layout::class)]
#[CoversClass(LayoutColumn::class)]
class LayoutTest extends TestCase
{
	protected function layout(): LayoutObject
	{
		return new LayoutObject([
			'attrs'   => ['foo' => 'bar'],
			'columns' => [['width' => '1/1']]
		]);
	}

	public function testUnknownNameReadsAttribute(): void
	{
		$this->assertSame('bar', (new Layout($this->layout()))->foo()->value());
	}

	public function testLayoutColumnRefusesUnknownName(): void
	{
		$this->expectException(PermissionException::class);
		(new LayoutColumn($this->layout()->columns()->first()))->foo();
	}
}
