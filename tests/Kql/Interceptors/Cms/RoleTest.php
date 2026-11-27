<?php

namespace Kirby\Kql\Interceptors\Cms;

use Kirby\Cms\Role as RoleObject;
use Kirby\Kql\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Role::class)]
class RoleTest extends TestCase
{
	protected function role(): Role
	{
		return new Role(new RoleObject([
			'name'        => 'editor',
			'permissions' => [
				'pages' => [
					'delete' => false
				]
			]
		]));
	}

	public function testPermissions(): void
	{
		$permissions = $this->role()->permissions();

		$this->assertIsArray($permissions);
		$this->assertFalse($permissions['pages']['delete']);
		$this->assertTrue($permissions['pages']['create']);
	}

	public function testToArray(): void
	{
		$this->assertSame([
			'description' => null,
			'id'          => 'editor',
			'name'        => 'editor',
			'title'       => 'Editor',
		], $this->role()->toArray());
	}
}
