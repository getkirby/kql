<?php

namespace Kirby\Kql\Interceptors\Cms;

use Kirby\Cms\Page as PageObject;
use Kirby\Content\Field;
use Kirby\Exception\PermissionException;
use Kirby\Kql\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Model::class)]
#[CoversClass(Page::class)]
class PageTest extends TestCase
{
	protected function page(): Page
	{
		return new Page(new PageObject([
			'slug'     => 'test',
			'template' => 'article',
			'content'  => [
				'title' => 'Test',
				'text'  => 'Hello'
			]
		]));
	}

	public function testContentField(): void
	{
		$field = $this->page()->text();

		$this->assertInstanceOf(Field::class, $field);
		$this->assertSame('Hello', $field->value());
	}

	public function testForbiddenMethod(): void
	{
		$this->expectException(PermissionException::class);
		$this->expectExceptionMessage('The method "Kirby\Cms\Page::delete()" is not allowed in the API context');
		$this->page()->delete();
	}

	public function testIntendedTemplate(): void
	{
		$this->assertSame('article', $this->page()->intendedTemplate());
	}

	public function testTemplate(): void
	{
		// falls back to the default template, as
		// there is no article template installed
		$this->assertSame('default', $this->page()->template());
	}

	public function testToArray(): void
	{
		$result = $this->page()->toArray();

		$this->assertSame('test', $result['id']);
		$this->assertSame('article', $result['intendedTemplate']);
		$this->assertSame('default', $result['template']);
		$this->assertSame('Test', $result['title']);
		$this->assertSame(['title' => 'Test', 'text' => 'Hello'], $result['content']);
	}
}
