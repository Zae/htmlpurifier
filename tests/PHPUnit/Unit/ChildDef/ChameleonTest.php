<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\ChildDef;

use HTMLPurifier\ChildDef\Chameleon;

/**
 * Class ChameleonTest
 *
 * @package HTMLPurifier\Tests\Unit\ChildDef
 */
class ChameleonTest extends TestCase
{
    private $isInline;

    protected function setUp(): void
    {
        parent::setUp();

        $this->obj = new Chameleon(
            'b | i',      // allowed only when in inline context
            'b | i | div' // allowed only when in block context
        );
        $this->context->register('IsInline', $this->isInline);
    }

    /**
     * @test
     */
    public function testInlineAlwaysAllowed(): void
    {
        $this->isInline = true;
        $this->assertResult(
            '<b>Allowed.</b>'
        );
    }

    /**
     * @test
     */
    public function testBlockNotAllowedInInline(): void
    {
        $this->isInline = true;
        $this->assertResult(
            '<div>Not allowed.</div>', ''
        );
    }

    /**
     * @test
     */
    public function testBlockAllowedInNonInline(): void
    {
        $this->isInline = false;
        $this->assertResult(
            '<div>Allowed.</div>'
        );
    }
}
