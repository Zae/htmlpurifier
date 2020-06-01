<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef\HTML;

use HTMLPurifier\AttrDef\HTML\FrameTarget;
use HTMLPurifier\Tests\Unit\AttrDef\TestCase;

/**
 * Class FrameTargetTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\HTML
 */
class FrameTargetTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->def = new FrameTarget();
    }

    /**
     * @test
     */
    public function testNoneAllowed(): void
    {
        $this->assertDef('', false);
        $this->assertDef('foo', false);
        $this->assertDef('_blank', false);
        $this->assertDef('baz', false);
    }

    /**
     * @test
     */
    public function test(): void
    {
        $this->config->set('Attr.AllowedFrameTargets', 'foo,_blank');
        $this->assertDef('', false);
        $this->assertDef('foo');
        $this->assertDef('_blank');
        $this->assertDef('baz', false);
    }
}
