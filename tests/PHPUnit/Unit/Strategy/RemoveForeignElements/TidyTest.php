<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Strategy\RemoveForeignElements;

use HTMLPurifier\Strategy\RemoveForeignElements;
use HTMLPurifier\Tests\Unit\Strategy\TestCase;

/**
 * Class TidyTest
 *
 * @package HTMLPurifier\Tests\Unit\Strategy\RemoveForeignElements
 */
class TidyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new RemoveForeignElements();
        $this->config->set('HTML.TidyLevel', 'heavy');
    }

    /**
     * @test
     */
    public function testCenterTransform(): void
    {
        $this->assertResult(
            '<center>Look I am Centered!</center>',
            '<div style="text-align:center;">Look I am Centered!</div>'
        );
    }

    /**
     * @test
     */
    public function testFontTransform(): void
    {
        $this->assertResult(
            '<font color="red" face="Arial" size="6">Big Warning!</font>',
            '<span style="color:red;font-family:Arial;font-size:xx-large;">Big' .
            ' Warning!</span>'
        );
    }

    /**
     * @test
     */
    public function testTransformToForbiddenElement(): void
    {
        $this->config->set('HTML.Allowed', 'div');
        $this->assertResult(
            '<font color="red" face="Arial" size="6">Big Warning!</font>',
            'Big Warning!'
        );
    }

    /**
     * @test
     */
    public function testMenuTransform(): void
    {
        $this->assertResult(
            '<menu><li>Item 1</li></menu>',
            '<ul><li>Item 1</li></ul>'
        );
    }
}
