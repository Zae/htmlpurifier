<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef\HTML;

use HTMLPurifier\AttrDef\HTML\Nmtokens;
use HTMLPurifier\Tests\Unit\AttrDef\TestCase;

/**
 * Class NmtokensTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\HTML
 */
class NmtokensTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->def = new Nmtokens();
    }

    /**
     * @test
     */
    public function testDefault(): void
    {
        $this->assertDef('valid');
        $this->assertDef('a0-_');
        $this->assertDef('-valid');
        $this->assertDef('_valid');
        $this->assertDef('double valid');

        $this->assertDef('0invalid', false);
        $this->assertDef('-0', false);

        // test conditional replacement
        $this->assertDef('validassoc 0invalid', 'validassoc');

        // test whitespace leniency
        $this->assertDef(" double\nvalid\r", 'double valid');

        // test case sensitivity
        $this->assertDef('VALID');
    }
}
