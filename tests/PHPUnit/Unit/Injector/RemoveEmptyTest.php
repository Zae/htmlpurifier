<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Injector;

/**
 * Class RemoveEmptyTest
 *
 * @package HTMLPurifier\Tests\Unit\Injector
 */
class RemoveEmptyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->config->set('AutoFormat.RemoveEmpty', true);
    }

    /**
     * @test
     */
    public function testPreserve(): void
    {
        $this->assertResult('<b>asdf</b>');
    }

    /**
     * @test
     */
    public function testRemove(): void
    {
        $this->assertResult('<b></b>', '');
    }

    /**
     * @test
     */
    public function testRemoveWithSpace(): void
    {
        $this->assertResult('<b>   </b>', '');
    }

    /**
     * @test
     */
    public function testRemoveWithAttr(): void
    {
        $this->assertResult('<b class="asdf"></b>', '');
    }

    /**
     * @test
     */
    public function testRemoveIdAndName(): void
    {
        $this->assertResult('<a id="asdf" name="asdf"></a>', '');
    }

    /**
     * @test
     */
    public function testPreserveColgroup(): void
    {
        $this->assertResult('<colgroup></colgroup>');
    }

    /**
     * @test
     */
    public function testPreserveId(): void
    {
        $this->config->set('Attr.EnableID', true);
        $this->assertResult('<a id="asdf"></a>');
    }

    /**
     * @test
     */
    public function testPreserveName(): void
    {
        $this->config->set('Attr.EnableID', true);
        $this->assertResult('<a name="asdf"></a>');
    }

    /**
     * @test
     */
    public function testRemoveNested(): void
    {
        $this->assertResult('<b><i></i></b>', '');
    }

    /**
     * @test
     */
    public function testRemoveNested2(): void
    {
        $this->assertResult('<b><i><u></u></i></b>', '');
    }

    /**
     * @test
     */
    public function testRemoveNested3(): void
    {
        $this->assertResult('<b> <i> <u> </u> </i> </b>', '');
    }

    /**
     * @test
     */
    public function testRemoveNbsp(): void
    {
        $this->config->set('AutoFormat.RemoveEmpty.RemoveNbsp', true);
        $this->assertResult('<b>&nbsp;</b>', '');
    }

    /**
     * @test
     */
    public function testRemoveNbspMix(): void
    {
        $this->config->set('AutoFormat.RemoveEmpty.RemoveNbsp', true);
        $this->assertResult('<b>&nbsp;   &nbsp;</b>', '');
    }

    /**
     * @test
     */
    public function testRemoveLi(): void
    {
        $this->assertResult("<ul><li>\n\n\n</li></ul>", '');
    }

    /**
     * @test
     */
    public function testDontRemoveNbsp(): void
    {
        $this->config->set('AutoFormat.RemoveEmpty.RemoveNbsp', true);
        $this->assertResult('<td>&nbsp;</b>', "<td>\xC2\xA0</td>");
    }

    /**
     * @test
     */
    public function testRemoveNbspExceptionsSpecial(): void
    {
        $this->config->set('AutoFormat.RemoveEmpty.RemoveNbsp', true);
        $this->config->set('AutoFormat.RemoveEmpty.RemoveNbsp.Exceptions', 'b');
        $this->assertResult('<b>&nbsp;</b>', "<b>\xC2\xA0</b>");
    }

    /**
     * @test
     */
    public function testRemoveIframe(): void
    {
        $this->config->set('HTML.SafeIframe', true);
        $this->assertResult('<iframe></iframe>', '');
    }

    /**
     * @test
     */
    public function testNoRemoveIframe(): void
    {
        $this->config->set('HTML.SafeIframe', true);
        $this->assertResult('<iframe src="http://google.com"></iframe>', '');
    }

    /**
     * @test
     */
    public function testRemoveDisallowedIframe(): void
    {
        $this->config->set('HTML.SafeIframe', true);
        $this->config->set('URI.SafeIframeRegexp', '%^http://www.youtube.com/embed/%');
        $this->assertResult('<iframe src="http://google.com"></iframe>', '');
    }
}
