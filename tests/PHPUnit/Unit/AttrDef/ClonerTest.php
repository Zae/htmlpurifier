<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef;

use HTMLPurifier\AttrDef\Cloner;
use HTMLPurifier\AttrDef\CSS;

/**
 * Class ClonerTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef
 */
class ClonerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function testCssClone(): void
    {
        $this->def = new Cloner(new CSS());

        $this->assertDef('text-align:right;');
    }
}
