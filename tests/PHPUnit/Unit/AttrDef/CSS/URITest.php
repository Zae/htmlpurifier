<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef\CSS;

use HTMLPurifier\AttrDef\CSS\URI;
use HTMLPurifier\Tests\Unit\AttrDef\TestCase;

/**
 * Class URITest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\CSS
 */
class URITest extends TestCase
{
    /**
     * @test
     */
    public function test(): void
    {
        $this->def = new URI();

        $this->assertDef('', false);

        // we could be nice but we won't be
        $this->assertDef('http://www.example.com/', false);

        $this->assertDef('url(', false);
        $this->assertDef('url("")', true);

        $result = 'url("http://www.example.com/")';
        $this->assertDef('url(http://www.example.com/)', $result);
        $this->assertDef('url("http://www.example.com/")', $result);
        $this->assertDef("url('http://www.example.com/')", $result);
        $this->assertDef('  url(  "http://www.example.com/" )   ', $result);
        $this->assertDef(
            "url(http://www.example.com/foo,bar\)\'\()",
            'url("http://www.example.com/foo,bar%29%27%28")'
        );
    }
}
