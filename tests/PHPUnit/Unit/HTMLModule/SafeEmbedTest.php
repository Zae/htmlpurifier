<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\HTMLModule;

/**
 * Class SafeEmbedTest
 *
 * @package HTMLPurifier\Tests\Unit\HTMLModule
 */
class SafeEmbedTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $def = $this->config->getHTMLDefinition(true);
        $def->manager->addModule('SafeEmbed');
    }

    /**
     * @test
     */
    public function testMinimal(): void
    {
        $this->assertResult(
            '<embed src="http://www.youtube.com/v/RVtEQxH7PWA&amp;hl=en" />',
            '<embed src="http://www.youtube.com/v/RVtEQxH7PWA&amp;hl=en" allowscriptaccess="never" allownetworking="internal" type="application/x-shockwave-flash" />'
        );
    }

    /**
     * @test
     */
    public function testYouTube(): void
    {
        $this->assertResult(
            '<embed src="http://www.youtube.com/v/RVtEQxH7PWA&amp;hl=en" type="application/x-shockwave-flash" width="425" height="344"></embed>',
            '<embed src="http://www.youtube.com/v/RVtEQxH7PWA&amp;hl=en" type="application/x-shockwave-flash" width="425" height="344" allowscriptaccess="never" allownetworking="internal" />'
        );
    }

    /**
     * @test
     */
    public function testMalicious(): void
    {
        $this->assertResult(
            '<embed src="http://example.com/bad.swf" type="application/x-shockwave-flash" width="9999999" height="3499994" allowscriptaccess="always" allownetworking="always" />',
            '<embed src="http://example.com/bad.swf" type="application/x-shockwave-flash" width="1200" height="1200" allowscriptaccess="never" allownetworking="internal" />'
        );
    }

    /**
     * @test
     */
    public function testFull(): void
    {
        $this->assertResult(
            '<b><embed src="http://www.youtube.com/v/RVtEQxH7PWA&amp;hl=en" type="application/x-shockwave-flash" width="24" height="23" allowscriptaccess="never" allownetworking="internal" wmode="window" /></b>'
        );
    }
}
