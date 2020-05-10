<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef\CSS;

use HTMLPurifier\AttrDef\CSS\BackgroundPosition;
use HTMLPurifier\Tests\Unit\AttrDef\TestCase;

/**
 * Class BackgroundPositionTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\CSS
 */
class BackgroundPositionTest extends TestCase
{
    /**
     * @test
     */
    public function test(): void
    {
        $this->def = new BackgroundPosition();

        // explicitly cited in spec
        $this->assertDef('0% 0%');
        $this->assertDef('100% 100%');
        $this->assertDef('14% 84%');
        $this->assertDef('2cm 1cm');
        $this->assertDef('top');
        $this->assertDef('left');
        $this->assertDef('center');
        $this->assertDef('right');
        $this->assertDef('bottom');
        $this->assertDef('left top');
        $this->assertDef('center top');
        $this->assertDef('right top');
        $this->assertDef('left center');
        $this->assertDef('right center');
        $this->assertDef('left bottom');
        $this->assertDef('center bottom');
        $this->assertDef('right bottom');

        // reordered due to internal impl details
        $this->assertDef('top left', 'left top');
        $this->assertDef('top center', 'top');
        $this->assertDef('top right', 'right top');
        $this->assertDef('center left', 'left');
        $this->assertDef('center center', 'center');
        $this->assertDef('center right', 'right');
        $this->assertDef('bottom left', 'left bottom');
        $this->assertDef('bottom center', 'bottom');
        $this->assertDef('bottom right', 'right bottom');

        // more cases from the defined syntax
        $this->assertDef('1.32in 4ex');
        $this->assertDef('-14% -84.65%');
        $this->assertDef('-1in -4ex');
        $this->assertDef('-1pc 2.3%');

        // keyword mixing
        $this->assertDef('3em top');
        $this->assertDef('left 50%');

        // fixable keyword mixing
        $this->assertDef('top 3em', '3em top');
        $this->assertDef('50% left', 'left 50%');

        // whitespace collapsing
        $this->assertDef('3em  top', '3em top');
        $this->assertDef("left\n \t foo  ", 'left');

        // invalid uses (we're going to be strict on these)
        $this->assertDef('foo bar', false);
        $this->assertDef('left left', 'left');
        $this->assertDef('left right top bottom center left', 'left bottom');
        $this->assertDef('0fr 9%', '9%');
    }
}
