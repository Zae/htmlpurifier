<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\HTMLModule;

/**
 * Class ObjectTest
 *
 * @package HTMLPurifier\Tests\Unit\HTMLModule
 */
class ObjectTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->config->set('HTML.Trusted', true);
    }

    /**
     * @test
     */
    public function testDefaultRemoval(): void
    {
        $this->config->set('HTML.Trusted', false);
        $this->assertResult('<object></object>', '');
    }

    /**
     * @test
     */
    public function testMinimal(): void
    {
        $this->assertResult('<object></object>');
    }

    /**
     * @test
     */
    public function testStandardUseCase(): void
    {
        $this->assertResult(
            '<object type="video/x-ms-wmv" data="http://domain.com/video.wmv" width="320" height="256">
<param name="src" value="http://domain.com/video.wmv" />
<param name="autostart" value="false" />
<param name="controller" value="true" />
<param name="pluginurl" value="http://www.microsoft.com/Windows/MediaPlayer/" />
<a href="http://www.microsoft.com/Windows/MediaPlayer/">Windows Media player required</a>
</object>'
        );
    }

    // more test-cases?
}
