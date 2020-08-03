<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef\CSS;

use HTMLPurifier\AttrDef\CSS\Ident;
use HTMLPurifier\Tests\Unit\AttrDef\TestCase;

/**
 * Class IdentTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef
 */
class IdentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->def = new Ident();
    }

    /**
     * @test
     */
    public function test(): void
    {
        $this->markAsRisky();

        $this->assertDef('', false);

        $this->assertDef('nono79');
        $this->assertDef('ground-level');
        $this->assertDef('-test');
        $this->assertDef('_internal');

        /*
         * Fixme: These really should be valid, but are not...
         *  https://developer.mozilla.org/en-US/docs/Web/CSS/custom-ident
         */
//        $this->assertDef("\22 toto");
//        $this->assertDef('bili\.bob');

        $this->assertDef('34rem', false);
        $this->assertDef('-12rad', false);
        $this->assertDef('bili.bob', false);
        $this->assertDef('--toto', false);
        $this->assertDef("'bilibob'", false);
        $this->assertDef('"bilibob"', false);
    }
}
