<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\HTMLModule;

/**
 * Class SafeScriptingTest
 *
 * @package HTMLPurifier\Tests\Unit\HTMLModule
 */
class SafeScriptingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->config->set('HTML.SafeScripting', ['http://localhost/foo.js']);
    }

    /**
     * @test
     */
    public function testMinimal(): void
    {
        $this->assertResult(
            '<script></script>',
            ''
        );
    }

    /**
     * @test
     */
    public function testGood(): void
    {
        $this->assertResult(
            '<script type="text/javascript" src="http://localhost/foo.js"></script>'
        );
    }

    /**
     * @test
     */
    public function testGoodWithAutoclosedTag(): void
    {
        $this->assertResult(
            '<script type="text/javascript" src="http://localhost/foo.js"/>',
            '<script type="text/javascript" src="http://localhost/foo.js"></script>'
        );
    }

    /**
     * @test
     */
    public function testBad(): void
    {
        $this->assertResult(
            '<script type="text/javascript" src="http://localhost/foobar.js" />',
            ''
        );
        $this->assertResult(
            '<script type="text/javascript" src="http://localhost/FOO.JS" />',
            ''
        );
    }
}
