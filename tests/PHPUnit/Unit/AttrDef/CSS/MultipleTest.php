<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef\CSS;

use HTMLPurifier\AttrDef\CSS\Multiple;
use HTMLPurifier\AttrDef\Integer;
use HTMLPurifier\Tests\Unit\AttrDef\TestCase;

/**
 * Class MultipleTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\CSS
 */
class MultipleTest extends TestCase
{
    /**
     * @test
     */
    public function test(): void
    {
        $this->def = new Multiple(
            new Integer()
        );

        $this->assertDef('1 2 3 4');
        $this->assertDef('6');
        $this->assertDef('4 5');
        $this->assertDef('  2  54 2 3', '2 54 2 3');
        $this->assertDef("6\r3", '6 3');

        $this->assertDef('asdf', false);
        $this->assertDef('a s d f', false);
        $this->assertDef('1 2 3 4 5', '1 2 3 4');
        $this->assertDef('1 2 invalid 3', '1 2 3');
    }
}
