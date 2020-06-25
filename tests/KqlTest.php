<?php

namespace Kirby\Kql;

use Kirby\Cms\App;
use PHPUnit\Framework\TestCase;

class KqlTest extends TestCase
{
    public function setUp(): void
    {
        $this->app = new App([
            'roots' => [
                'index' => '/dev/null'
            ],
            'site' => [
                'content' => [
                    'title' => 'Test Site'
                ]
            ]
        ]);
    }

    public function testRun()
    {
        $result   = Kql::run('site.title');
        $expected = 'Test Site';

        $this->assertSame($expected, $result);
    }
}
