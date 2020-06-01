<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef\HTML;

use HTMLPurifier\AttrDef\HTML\Boolean;
use HTMLPurifier\Tests\Unit\AttrDef\TestCase;

/**
 * Class BoolTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\HTML
 */
class BoolTest extends TestCase
{
    /**
     * @test
     */
    public function test(): void
    {
        $this->def = new Boolean('foo');
        $this->assertDef('foo');
        $this->assertDef('', 'foo');
        $this->assertDef('bar', 'foo');
    }

    /**
     * @test
     */
    public function test_make(): void
    {
        $factory = new Boolean();
        $def = $factory->make('foo');
        $def2 = new Boolean('foo');

        static::assertEquals($def, $def2);
    }
}
