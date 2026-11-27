<?php

namespace Kirby\Kql\Interceptors\Content;

use Kirby\Cms\Page;
use Kirby\Content\VersionId as VersionIdObject;
use Kirby\Exception\PermissionException;
use Kirby\Kql\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Version::class)]
#[CoversClass(VersionId::class)]
class VersionTest extends TestCase
{
	public function testForbiddenMethod(): void
	{
		$version = new Version((new Page(['slug' => 'test']))->version());

		$this->expectException(PermissionException::class);
		$this->expectExceptionMessage('The method "Kirby\Content\Version::delete()" is not allowed in the API context');
		$version->delete();
	}

	public function testToArray(): void
	{
		$version = new Version((new Page(['slug' => 'test']))->version());

		$this->assertSame(
			['content', 'id', 'exists', 'isLatest', 'lock', 'modified'],
			array_keys($version->toArray())
		);
	}

	public function testVersionIdToResponse(): void
	{
		$this->assertSame('latest', (new VersionId(VersionIdObject::latest()))->toResponse());
		$this->assertSame('changes', (new VersionId(VersionIdObject::changes()))->toResponse());
	}
}
