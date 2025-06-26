<?php

namespace Kirby\Kql;

use Kirby\Exception\PermissionException;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Kirby\Kql\Query::class)]
class QueryTest extends TestCase
{
	public function testIntercept(): void
	{
		// non-object
		$query = new Query('foo.bar');
		$result = $query->intercept('test');
		$this->assertSame('test', $result);

		// object
		$object = new TestObject();
		$this->expectException(PermissionException::class);
		$this->expectExceptionMessage('Access to the class "Kirby\Kql\TestObject" is not supported');
		$query->intercept($object);
	}
}
