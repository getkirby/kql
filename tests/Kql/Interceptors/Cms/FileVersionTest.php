<?php

namespace Kirby\Kql\Interceptors\Cms;

use Kirby\Cms\File as FileObject;
use Kirby\Cms\FileVersion as FileVersionObject;
use Kirby\Cms\Page;
use Kirby\Exception\PermissionException;
use Kirby\Filesystem\Asset;
use Kirby\Filesystem\Dir;
use Kirby\Kql\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FileVersion::class)]
class FileVersionTest extends TestCase
{
	public const TMP = __DIR__ . '/tmp';

	protected function thumb(FileObject|Asset|null $original = null): FileVersion
	{
		$original ??= new FileObject([
			'filename' => 'test.jpg',
			'parent'   => new Page(['slug' => 'test']),
			'content'  => ['alt' => 'Alt text']
		]);

		return new FileVersion(new FileVersionObject([
			'original' => $original,
			'root'     => '/dev/null/test-20x.jpg',
			'url'      => '/media/test-20x.jpg'
		]));
	}

	protected function thumbOnDisk(): FileVersion
	{
		Dir::make(static::TMP);
		imagepng(imagecreatetruecolor(20, 10), static::TMP . '/test-20x.png');

		return new FileVersion(new FileVersionObject([
			'original' => new FileObject([
				'filename' => 'test.png',
				'parent'   => new Page(['slug' => 'test']),
				'content'  => ['template' => 'image']
			]),
			'root'     => static::TMP . '/test-20x.png',
			'url'      => '/media/test-20x.png'
		]));
	}

	public function tearDown(): void
	{
		Dir::remove(static::TMP);
	}

	public function testAltReadsOriginalContentField(): void
	{
		$this->assertSame('Alt text', $this->thumb()->alt()->value());
	}

	public function testDimensionsReturnsArray(): void
	{
		$this->assertSame([
			'width'       => 20,
			'height'      => 10,
			'ratio'       => 2.0,
			'orientation' => 'landscape',
		], $this->thumbOnDisk()->dimensions());
	}

	public function testRefusesAltWithAssetOriginal(): void
	{
		$this->expectException(PermissionException::class);
		$this->thumb(new Asset('assets/test.jpg'))->alt();
	}

	public function testToArrayRendersThumb(): void
	{
		$thumb = $this->thumbOnDisk();

		$this->assertSame([
			'extension' => 'png',
			'filename'  => 'test-20x.png',
			'height'    => 10,
			'id'        => 'test/test-20x.png',
			'mime'      => 'image/png',
			'niceSize'  => $thumb->niceSize(),
			'template'  => 'image',
			'type'      => 'image',
			'url'       => '/media/test-20x.png',
			'width'     => 20
		], $thumb->toArray());
	}

	public function testOriginalReturnsFile(): void
	{
		$this->assertSame('test.jpg', $this->thumb()->original()->filename());
	}

	public function testRefusesUnlistedAssetMethod(): void
	{
		$this->expectException(PermissionException::class);
		$this->thumb()->read();
	}
}
