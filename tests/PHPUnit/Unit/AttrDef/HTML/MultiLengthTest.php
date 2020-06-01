<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef\HTML;

use HTMLPurifier\AttrDef\HTML\MultiLength;

/**
 * Class MultiLengthTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\HTML
 */
class MultiLengthTest extends LengthTest
{
    protected function setUp(): void
    {
        $this->def = new MultiLength();
    }

    /**
     * @test
     */
    public function test(): void
    {
        // length check
        parent::test();

        $this->assertDef('*');
        $this->assertDef('1*', '*');
        $this->assertDef('56*');

        $this->assertDef('**', false); // plain old bad

        $this->assertDef('5.4*', '5*'); // no decimals
        $this->assertDef('-3*', false); // no negatives
    }
}
