<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Injector;

use HTMLPurifier\Injector\SafeObject;

/**
 * Class SafeObjectTest
 *
 * @package HTMLPurifier\Tests\Unit\Injector
 */
class SafeObjectTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // there is no AutoFormat.SafeObject directive
        $this->config->set('AutoFormat.Custom', [new SafeObject()]);
        $this->config->set('HTML.Trusted', true);
    }

    /**
     * @test
     */
    public function testPreserve(): void
    {
        $this->assertResult(
            '<b>asdf</b>'
        );
    }

    /**
     * @test
     */
    public function testRemoveStrayParam(): void
    {
        $this->assertResult(
            '<param />',
            ''
        );
    }

    /**
     * @test
     */
    public function testEditObjectParam(): void
    {
        $this->assertResult(
            '<object></object>',
            '<object><param name="allowScriptAccess" value="never" /><param name="allowNetworking" value="internal" /></object>'
        );
    }

    /**
     * @test
     */
    public function testIgnoreStrayParam(): void
    {
        $this->assertResult(
            '<object><param /></object>',
            '<object><param name="allowScriptAccess" value="never" /><param name="allowNetworking" value="internal" /></object>'
        );
    }

    /**
     * @test
     */
    public function testIgnoreDuplicates(): void
    {
        $this->assertResult(
            '<object><param name="allowScriptAccess" value="never" /><param name="allowNetworking" value="internal" /></object>'
        );
    }

    /**
     * @test
     */
    public function testIgnoreBogusData(): void
    {
        $this->assertResult(
            '<object><param name="allowscriptaccess" value="always" /><param name="allowNetworking" value="always" /></object>',
            '<object><param name="allowScriptAccess" value="never" /><param name="allowNetworking" value="internal" /></object>'
        );
    }

    /**
     * @test
     */
    public function testIgnoreInvalidData(): void
    {
        $this->assertResult(
            '<object><param name="foo" value="bar" /></object>',
            '<object><param name="allowScriptAccess" value="never" /><param name="allowNetworking" value="internal" /></object>'
        );
    }

    /**
     * @test
     */
    public function testKeepValidData(): void
    {
        $this->assertResult(
            '<object><param name="movie" value="bar" /></object>',
            '<object data="bar"><param name="allowScriptAccess" value="never" /><param name="allowNetworking" value="internal" /><param name="movie" value="bar" /></object>'
        );
    }

    /**
     * @test
     */
    public function testNested(): void
    {
        $this->assertResult(
            '<object><param name="allowScriptAccess" value="never" /><param name="allowNetworking" value="internal" /><object></object></object>',
            '<object><param name="allowScriptAccess" value="never" /><param name="allowNetworking" value="internal" /><object><param name="allowScriptAccess" value="never" /><param name="allowNetworking" value="internal" /></object></object>'
        );
    }

    /**
     * @test
     */
    public function testNotActuallyNested(): void
    {
        $this->assertResult(
            '<object><p><param name="allowScriptAccess" value="never" /><param name="allowNetworking" value="internal" /></p></object>',
            '<object><param name="allowScriptAccess" value="never" /><param name="allowNetworking" value="internal" /><p></p></object>'
        );
    }

    /**
     * @test
     */
    public function testCaseInsensitive(): void
    {
        $this->assertResult(
            '<object><param name="allowScriptAccess" value="never" /><param name="allowNetworking" value="internal" /><param name="flashVars" value="a" /><param name="FlashVars" value="b" /></object>'
        );
    }
}
