<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Strategy\ValidateAttributes;

use HTMLPurifier\Strategy\ValidateAttributes;
use HTMLPurifier\Tests\Unit\Strategy\TestCase;

/**
 * Class IDTest
 *
 * @package HTMLPurifier\Tests\Unit\Strategy\ValidateAttributes
 */
class IDTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->obj = new ValidateAttributes();
        $this->config->set('Attr.EnableID', true);
    }

    /**
     * @test
     */
    public function testPreserveIDWhenEnabled(): void
    {
        $this->assertResult('<div id="valid">Preserve the ID.</div>');
    }

    /**
     * @test
     */
    public function testRemoveInvalidID(): void
    {
        $this->assertResult(
            '<div id="0invalid">Kill the ID.</div>',
            '<div>Kill the ID.</div>'
        );
    }

    /**
     * @test
     */
    public function testRemoveDuplicateID(): void
    {
        $this->assertResult(
            '<div id="valid">Valid</div><div id="valid">Invalid</div>',
            '<div id="valid">Valid</div><div>Invalid</div>'
        );
    }

    /**
     * @test
     */
    public function testAttributeKeyCaseInsensitivity(): void
    {
        $this->assertResult(
            '<div ID="valid">Convert ID to lowercase.</div>',
            '<div id="valid">Convert ID to lowercase.</div>'
        );
    }

    /**
     * @test
     */
    public function testTrimWhitespace(): void
    {
        $this->assertResult(
            '<div id=" valid ">Trim whitespace.</div>',
            '<div id="valid">Trim whitespace.</div>'
        );
    }

    /**
     * @test
     */
    public function testIDBlacklist(): void
    {
        $this->config->set('Attr.IDBlacklist', ['invalid']);
        $this->assertResult(
            '<div id="invalid">Invalid</div>',
            '<div>Invalid</div>'
        );
    }

    /**
     * @test
     */
    public function testNameConvertedToID(): void
    {
        $this->config->set('HTML.TidyLevel', 'heavy');
        $this->assertResult(
            '<a name="foobar" />',
            '<a id="foobar" />'
        );
    }
}
