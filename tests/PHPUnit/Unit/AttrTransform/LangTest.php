<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrTransform;

use HTMLPurifier\AttrTransform\Lang;

/**
 * Class LangTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrTransform
 */
class LangTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new Lang();
    }

    /**
     * @test
     */
    public function testEmptyInput(): void
    {
        $this->assertResult([]);
    }

    /**
     * @test
     */
    public function testCopyLangToXMLLang(): void
    {
        $this->assertResult(
            ['lang' => 'en'],
            ['lang' => 'en', 'xml:lang' => 'en']
        );
    }

    /**
     * @test
     */
    public function testPreserveAttributes(): void
    {
        $this->assertResult(
            ['src' => 'vert.png', 'lang' => 'fr'],
            ['src' => 'vert.png', 'lang' => 'fr', 'xml:lang' => 'fr']
        );
    }

    /**
     * @test
     */
    public function testCopyXMLLangToLang(): void
    {
        $this->assertResult(
            ['xml:lang' => 'en'],
            ['xml:lang' => 'en', 'lang' => 'en']
        );
    }

    /**
     * @test
     */
    public function testXMLLangOverridesLang(): void
    {
        $this->assertResult(
            ['lang' => 'fr', 'xml:lang' => 'de'],
            ['lang' => 'de', 'xml:lang' => 'de']
        );
    }
}
