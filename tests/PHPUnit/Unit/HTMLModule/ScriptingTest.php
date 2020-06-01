<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\HTMLModule;

/**
 * Class ScriptingTest
 *
 * @package HTMLPurifier\Tests\Unit\HTMLModule
 */
class ScriptingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->config->set('HTML.Trusted', true);
        $this->config->set('Output.CommentScriptContents', false);
    }

    /**
     * @test
     */
    public function testDefaultRemoval(): void
    {
        $this->config->set('HTML.Trusted', false);
        $this->assertResult(
            '<script type="text/javascript">foo();</script>',
            ''
        );
    }

    /**
     * @test
     */
    public function testPreserve(): void
    {
        $this->assertResult(
            '<script type="text/javascript">foo();</script>'
        );
    }

    /**
     * @test
     */
    public function testCDATAEnclosure(): void
    {
        $this->assertResult(
            '<script type="text/javascript">//<![CDATA[
alert("<This is compatible with XHTML>");
//]]></script>'
        );
    }

    /**
     * @test
     */
    public function testAllAttributes(): void
    {
        $this->assertResult(
            '<script
                defer="defer"
                src="test.js"
                type="text/javascript"
            >PCDATA</script>'
        );
    }

    /**
     * @test
     */
    public function testUnsupportedAttributes(): void
    {
        $this->assertResult(
            '<script
                type="text/javascript"
                charset="utf-8"
            >PCDATA</script>',
            '<script type="text/javascript">PCDATA</script>'
        );
    }
}
