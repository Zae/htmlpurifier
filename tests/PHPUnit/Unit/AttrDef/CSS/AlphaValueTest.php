<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef\CSS;

use HTMLPurifier\AttrDef\CSS\AlphaValue;
use HTMLPurifier\Tests\Unit\AttrDef\TestCase;

/**
 * Class AlphaValueTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\CSS
 */
class AlphaValueTest extends TestCase
{
    public function test()
    {
        $this->def = new AlphaValue();

        $this->assertDef('0');
        $this->assertDef('1');
        $this->assertDef('0.2');

        // clamping to [0.0, 1,0]
        $this->assertDef('1.2', '1');
        $this->assertDef('-3', '0');

        $this->assertDef('0.0', '0');
        $this->assertDef('1.0', '1');
        $this->assertDef('000', '0');

        $this->assertDef('asdf', false);
    }
}
