<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef;

use HTMLPurifier\AttrDef\Text;

/**
 * Class TextTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef
 */
class TextTest extends TestCase
{
    /**
     * @test
     */
    public function test(): void
    {
        $this->def = new Text();

        $this->assertDef('This is spiffy text!');
        $this->assertDef(" Casual\tCDATA parse\ncheck. ", 'Casual CDATA parse check.');
    }
}
