<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef\CSS;

use HTMLPurifier\AttrDef\CSS\ListStyle;
use HTMLPurifier\Config;
use HTMLPurifier\Tests\Unit\AttrDef\TestCase;

/**
 * Class ListStyleTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\CSS
 */
class ListStyleTest extends TestCase
{
    /**
     * @test
     * @throws \HTMLPurifier\Exception
     */
    public function test(): void
    {
        $config = Config::createDefault();
        $this->def = new ListStyle($config);

        $this->assertDef('lower-alpha');
        $this->assertDef('upper-roman inside');
        $this->assertDef('circle outside');
        $this->assertDef('inside');
        $this->assertDef('none');
        $this->assertDef('url("foo.gif")');
        $this->assertDef('circle url("foo.gif") inside');

        // invalid values
        $this->assertDef('outside inside', 'outside');

        // ordering
        $this->assertDef('url(foo.gif) none', 'none url("foo.gif")');
        $this->assertDef('circle lower-alpha', 'circle');
        // the spec is ambiguous about what happens in these
        // cases, so we're going off the W3C CSS validator
        $this->assertDef('disc none', 'disc');
        $this->assertDef('none disc', 'none');
    }
}
