<?php

namespace Kirby\Kql\Interceptors\Content;

use Kirby\Content\Content as ContentObject;
use Kirby\Content\Field as FieldObject;
use Kirby\Exception\PermissionException;
use Kirby\Kql\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Content::class)]
class ContentTest extends TestCase
{
	protected function content(): Content
	{
		return new Content(new ContentObject([
			'title' => 'Test',
			'text'  => 'Hello'
		]));
	}

	public function testAllowedMethod(): void
	{
		$this->assertSame(['title', 'text'], $this->content()->keys());
	}

	public function testField(): void
	{
		$field = $this->content()->text();

		$this->assertInstanceOf(FieldObject::class, $field);
		$this->assertSame('Hello', $field->value());
	}

	public function testForbiddenMethod(): void
	{
		$this->expectException(PermissionException::class);
		$this->expectExceptionMessage('The method "Kirby\Content\Content::update()" is not allowed in the API context');
		$this->content()->update(['title' => 'Hacked']);
	}

	public function testToArray(): void
	{
		$this->assertSame([
			'title' => 'Test',
			'text'  => 'Hello'
		], $this->content()->toArray());
	}
}
