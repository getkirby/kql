<?php

namespace Kirby\Kql\Interceptors\Toolkit;

use Kirby\Exception\PermissionException;
use Kirby\Kql\TestCase;
use Kirby\Toolkit\Obj as ObjObject;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Obj::class)]
class ObjTest extends TestCase
{
	public function testAllowedMethod(): void
	{
		$obj = new Obj(new ObjObject(['a' => 'b']));
		$this->assertSame('b', $obj->get('a'));
	}

	public function testForbiddenMethod(): void
	{
		$obj = new Obj(new ObjObject(['a' => 'b']));

		$this->expectException(PermissionException::class);
		$this->expectExceptionMessage('The method "Kirby\Toolkit\Obj::toKeys()" is not allowed in the API context');
		$obj->toKeys();
	}

	public function testToArray(): void
	{
		$obj = new Obj(new ObjObject(['a' => 'b']));
		$this->assertSame(['a' => 'b'], $obj->toArray());
	}
}
