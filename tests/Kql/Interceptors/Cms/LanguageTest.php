<?php

namespace Kirby\Kql\Interceptors\Cms;

use Kirby\Cms\Language as LanguageObject;
use Kirby\Kql\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Language::class)]
class LanguageTest extends TestCase
{
	protected function language(): Language
	{
		return new Language(new LanguageObject([
			'code'    => 'de',
			'default' => true,
			'locale'  => [LC_ALL => 'de_DE', LC_TIME => 'de_AT'],
			'name'    => 'Deutsch'
		]));
	}

	public function testLocaleNamesCategories(): void
	{
		$this->assertSame(
			['LC_ALL' => 'de_DE', 'LC_TIME' => 'de_AT'],
			$this->language()->locale()
		);
	}

	public function testRulesReturnsSlugRules(): void
	{
		$this->assertSame('ae', $this->language()->rules()['ä']);
	}

	public function testToArrayLeavesOutRules(): void
	{
		$this->assertSame([
			'code'      => 'de',
			'direction' => 'ltr',
			'isDefault' => true,
			'locale'    => ['LC_ALL' => 'de_DE', 'LC_TIME' => 'de_AT'],
			'name'      => 'Deutsch',
			'url'       => '/de'
		], $this->language()->toArray());
	}
}
