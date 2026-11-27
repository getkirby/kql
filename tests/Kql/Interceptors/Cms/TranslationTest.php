<?php

namespace Kirby\Kql\Interceptors\Cms;

use Kirby\Cms\Translation as TranslationObject;
use Kirby\Kql\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Translation::class)]
class TranslationTest extends TestCase
{
	public function testToArrayLeavesOutData(): void
	{
		$translation = new Translation(new TranslationObject('en', ['translation.name' => 'English']));

		$this->assertSame([
			'code'      => 'en',
			'direction' => 'ltr',
			'id'        => 'en',
			'name'      => 'English',
			'locale'    => 'en_EN',
			'author'    => 'Kirby'
		], $translation->toArray());
	}
}
