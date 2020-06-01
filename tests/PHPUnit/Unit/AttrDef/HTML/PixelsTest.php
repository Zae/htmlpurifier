<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef\HTML;

use HTMLPurifier\AttrDef\HTML\Pixels;
use HTMLPurifier\Tests\Unit\AttrDef\TestCase;

/**
 * Class PixelsTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\HTML
 */
class PixelsTest extends TestCase
{
    protected function setUp(): void
    {
        $this->def = new Pixels();
    }

    /**
     * @test
     */
    public function test(): void
    {
        $this->assertDef('1');
        $this->assertDef('0');

        $this->assertDef('2px', '2'); // rm px suffix

        $this->assertDef('dfs', false); // totally invalid value

        // conceivably we could repair this value, but we won't for now
        $this->assertDef('9in', false);

        // test trim
        $this->assertDef(' 45 ', '45');

        // no negatives
        $this->assertDef('-2', '0');

        // remove empty
        $this->assertDef('', false);

        // round down
        $this->assertDef('4.9', '4');
    }

    /**
     * @test
     */
    public function test_make(): void
    {
        $factory = new Pixels();
        $this->def = $factory->make('30');
        $this->assertDef('25');
        $this->assertDef('35', '30');
    }
}
