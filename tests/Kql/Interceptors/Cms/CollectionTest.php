<?php

namespace Kirby\Kql\Interceptors\Cms;

use Kirby\Content\Field;
use Kirby\Exception\PermissionException;
use Kirby\Kql\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Blocks::class)]
#[CoversClass(Collection::class)]
#[CoversClass(Layouts::class)]
#[CoversClass(Pages::class)]
#[CoversClass(Structure::class)]
class CollectionTest extends TestCase
{
	public function testAllowedMethod(): void
	{
		$pages = new Pages($this->app->site()->children());

		$this->assertSame(3, $pages->count());
		$this->assertSame('about', $pages->find('about')->slug());
	}

	public function testBlocksToArray(): void
	{
		$field  = new Field(null, 'blocks', json_encode([
			['type' => 'text', 'content' => ['text' => 'Hello']]
		]));
		$blocks = new Blocks($field->toBlocks());
		$result = $blocks->toArray();

		$this->assertCount(1, $result);
		$this->assertSame('text', $result[0]['type']);
		$this->assertSame(['text' => 'Hello'], $result[0]['content']);
	}

	public function testForbiddenMethod(): void
	{
		$pages = new Pages($this->app->site()->children());

		$this->expectException(PermissionException::class);
		$this->expectExceptionMessage('The method "Kirby\Cms\Pages::add()" is not allowed in the API context');
		$pages->add('test');
	}

	public function testLayoutsToArray(): void
	{
		$field   = new Field(null, 'layout', json_encode([
			['columns' => [['width' => '1/2', 'blocks' => []]]]
		]));
		$layouts = new Layouts($field->toLayouts());
		$result  = $layouts->toArray();

		$this->assertCount(1, $result);
		$this->assertSame('1/2', $result[0]['columns'][0]['width']);
	}

	public function testStructureToArray(): void
	{
		$field     = new Field(null, 'items', "- name: One\n- name: Two");
		$structure = new Structure($field->toStructure());

		$this->assertSame([
			['name' => 'One', 'id' => '0'],
			['name' => 'Two', 'id' => '1'],
		], $structure->toArray());
	}

	public function testToArray(): void
	{
		$pages = new Pages($this->app->site()->children());
		$this->assertSame(['projects', 'about', 'contact'], $pages->toArray());
	}
}
