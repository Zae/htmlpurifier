<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\HTMLModule;

/**
 * Class NameTest
 *
 * @package HTMLPurifier\Tests\Unit\HTMLModule
 */
class NameTest extends TestCase
{
    /**
     * @test
     */
    public function testBasicUse(): void
    {
        $this->config->set('Attr.EnableID', true);
        $this->assertResult(
            '<a name="foo">bar</a>'
        );
    }

    /**
     * @test
     */
    public function testCDATA(): void
    {
        $this->config->set('HTML.Attr.Name.UseCDATA', true);
        $this->assertResult(
            '<a name="2">Baz</a><a name="2">Bar</a>'
        );
    }

    /**
     * @test
     */
    public function testCDATAWithHeavyTidy(): void
    {
        $this->config->set('HTML.Attr.Name.UseCDATA', true);
        $this->config->set('HTML.TidyLevel', 'heavy');
        $this->assertResult('<a name="2">Baz</a>');
    }
}
