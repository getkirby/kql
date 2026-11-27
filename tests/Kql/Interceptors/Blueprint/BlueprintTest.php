<?php

namespace Kirby\Kql\Interceptors\Blueprint;

use Kirby\Blueprint\Blueprint as BlueprintObject;
use Kirby\Cms\Page;
use Kirby\Kql\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Blueprint::class)]
class BlueprintTest extends TestCase
{
	protected function blueprint(): Blueprint
	{
		return new Blueprint(new BlueprintObject([
			'model' => new Page(['slug' => 'test']),
			'name'  => 'test',
			'title' => 'Test',
			'tabs'  => [
				'content' => [
					'columns' => [
						[
							'width'  => '2/3',
							'fields' => [
								'text' => ['type' => 'textarea']
							]
						],
						[
							'width'  => '1/3',
							'fields' => [
								'date' => ['type' => 'date'],
								'tags' => ['type' => 'tags']
							]
						]
					]
				],
				'seo' => [
					'fields' => [
						'description' => ['type' => 'text']
					]
				]
			]
		]));
	}

	public function testFields(): void
	{
		$fields = $this->blueprint()->fields();

		$this->assertSame(['text', 'date', 'tags', 'description'], array_keys($fields));
	}

	public function testTab(): void
	{
		$tab = $this->blueprint()->tab('content');

		$this->assertSame('content', $tab['name']);
		$this->assertSame('Content', $tab['label']);
		$this->assertSame(['text'], $tab['columns'][0]['fields']);
		$this->assertSame(['date', 'tags'], $tab['columns'][1]['fields']);
	}

	public function testTabDefault(): void
	{
		$tab = $this->blueprint()->tab();
		$this->assertSame('content', $tab['name']);
	}

	public function testTabMissing(): void
	{
		$this->assertNull($this->blueprint()->tab('missing'));
	}

	public function testTabs(): void
	{
		$tabs = $this->blueprint()->tabs();

		$this->assertSame(['content', 'seo'], array_keys($tabs));
		$this->assertSame(['description'], $tabs['seo']['columns'][0]['fields']);
	}

	public function testToArray(): void
	{
		$result = $this->blueprint()->toArray();

		$this->assertSame('test', $result['name']);
		$this->assertSame('Test', $result['title']);
		$this->assertArrayNotHasKey('sections', $result);
		$this->assertSame(['content', 'seo'], array_keys($result['tabs']));
	}
}
