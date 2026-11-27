<?php

namespace Kirby\Kql\Interceptors\Cms;

use Kirby\Cms\File as FileObject;
use Kirby\Cms\Page;
use Kirby\Filesystem\Dir;
use Kirby\Kql\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(File::class)]
class FileTest extends TestCase
{
	public const TMP = __DIR__ . '/tmp';

	public function setUp(): void
	{
		parent::setUp();

		Dir::make(static::TMP . '/content/test');

		$image = imagecreatetruecolor(4, 2);
		imagepng($image, static::TMP . '/content/test/image.png');

		$this->app = $this->app->clone([
			'roots' => [
				'index' => static::TMP
			]
		]);
	}

	public function tearDown(): void
	{
		Dir::remove(static::TMP);
	}

	protected function file(): File
	{
		return new File(new FileObject([
			'filename' => 'image.png',
			'parent'   => new Page(['slug' => 'test'])
		]));
	}

	public function testDimensions(): void
	{
		$this->assertSame([
			'width'       => 4,
			'height'      => 2,
			'ratio'       => 2.0,
			'orientation' => 'landscape',
		], $this->file()->dimensions());
	}

	public function testExif(): void
	{
		$this->assertIsArray($this->file()->exif());
	}

	public function testToArray(): void
	{
		$result = $this->file()->toArray();

		$this->assertSame('png', $result['extension']);
		$this->assertSame('image.png', $result['filename']);
		$this->assertSame(2, $result['height']);
		$this->assertSame('test/image.png', $result['id']);
		$this->assertSame('image/png', $result['mime']);
		$this->assertSame('image', $result['type']);
		$this->assertSame(4, $result['width']);
	}
}
