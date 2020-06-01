<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef\HTML;

use HTMLPurifier\AttrDef\HTML\Classname;

/**
 * Class ClassTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\HTML
 */
class ClassTest extends NmtokensTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->def = new Classname();
    }

    /**
     * @test
     */
    public function testAllowedClasses(): void
    {
        $this->config->set('Attr.AllowedClasses', ['foo']);

        $this->assertDef('foo');
        $this->assertDef('bar', false);
        $this->assertDef('foo bar', 'foo');
    }

    /**
     * @test
     */
    public function testForbiddenClasses(): void
    {
        $this->config->set('Attr.ForbiddenClasses', ['bar']);

        $this->assertDef('foo');
        $this->assertDef('bar', false);
        $this->assertDef('foo bar', 'foo');
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

        $this->assertDef('0stillvalid');
        $this->assertDef('-0');

        // test conditional replacement
        $this->assertDef('validassoc 0valid', 'validassoc 0valid');

        // test whitespace leniency
        $this->assertDef(" double\nvalid\r", 'double valid');

        // test case sensitivity
        $this->assertDef('VALID');

        // test duplicate removal
        $this->assertDef('valid valid', 'valid');
    }

    /**
     * @test
     */
    public function testXHTML11Behavior(): void
    {
        $this->config->set('HTML.Doctype', 'XHTML 1.1');

        $this->assertDef('0invalid', false);
        $this->assertDef('valid valid', 'valid');
    }
}
