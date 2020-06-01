<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef\CSS;

use HTMLPurifier\AttrDef\CSS\TextDecoration;
use HTMLPurifier\Tests\Unit\AttrDef\TestCase;

/**
 * Class TextDecorationTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\CSS
 */
class TextDecorationTest extends TestCase
{
    /**
     * @test
     */
    public function testCaseInsensitive(): void
    {
        $this->def = new TextDecoration();

        $this->assertDef('none');
        $this->assertDef('none underline', 'underline');

        $this->assertDef('underline');
        $this->assertDef('overline');
        $this->assertDef('line-through overline underline');
        $this->assertDef('overline line-through');
        $this->assertDef('UNDERLINE', 'underline');
        $this->assertDef('  underline line-through ', 'underline line-through');

        $this->assertDef('foobar underline', 'underline');
        $this->assertDef('blink', false);
    }
}
