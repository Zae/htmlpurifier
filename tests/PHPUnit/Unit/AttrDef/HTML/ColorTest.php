<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef\HTML;

use HTMLPurifier\AttrDef\HTML\Color;
use HTMLPurifier\Tests\Unit\AttrDef\TestCase;

/**
 * Class ColorsTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\HTML
 */
class ColorTest extends TestCase
{
    /**
     * @test
     */
    public function test(): void
    {
        $this->def = new Color();

        $this->assertDef('', false);
        $this->assertDef('foo', false);
        $this->assertDef('43', false);
        $this->assertDef('red', '#FF0000');
        $this->assertDef('RED', '#FF0000');
        $this->assertDef('#FF0000');
        $this->assertDef('#453443');
        $this->assertDef('453443', '#453443');
        $this->assertDef('#345', '#334455');
        $this->assertDef('120', '#112200');
    }
}
