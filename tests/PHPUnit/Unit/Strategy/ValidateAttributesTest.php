<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Strategy;

use HTMLPurifier\Strategy\ValidateAttributes;

/**
 * Class ValidateAttributesTest
 *
 * @package HTMLPurifier\Tests\Unit\Strategy
 */
class ValidateAttributesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new ValidateAttributes();
    }

    /**
     * @test
     */
    public function testEmptyInput(): void
    {
        $this->assertResult('');
    }

    /**
     * @test
     */
    public function testRemoveIDByDefault(): void
    {
        $this->assertResult(
            '<div id="valid">Kill the ID.</div>',
            '<div>Kill the ID.</div>'
        );
    }

    /**
     * @test
     */
    public function testRemoveInvalidDir(): void
    {
        $this->assertResult(
            '<span dir="up-to-down">Bad dir.</span>',
            '<span>Bad dir.</span>'
        );
    }

    /**
     * @test
     */
    public function testPreserveValidClass(): void
    {
        $this->assertResult('<div class="valid">Valid</div>');
    }

    /**
     * @test
     */
    public function testSelectivelyRemoveInvalidClasses(): void
    {
        $this->config->set('HTML.Doctype', 'XHTML 1.1');
        $this->assertResult(
            '<div class="valid 0invalid">Keep valid.</div>',
            '<div class="valid">Keep valid.</div>'
        );
    }

    /**
     * @test
     */
    public function testPreserveTitle(): void
    {
        $this->assertResult(
            '<acronym title="PHP: Hypertext Preprocessor">PHP</acronym>'
        );
    }

    /**
     * @test
     */
    public function testAddXMLLang(): void
    {
        $this->assertResult(
            '<span lang="fr">La soupe.</span>',
            '<span lang="fr" xml:lang="fr">La soupe.</span>'
        );
    }

    /**
     * @test
     */
    public function testOnlyXMLLangInXHTML11(): void
    {
        $this->config->set('HTML.Doctype', 'XHTML 1.1');
        $this->assertResult(
            '<b lang="en">asdf</b>',
            '<b xml:lang="en">asdf</b>'
        );
    }

    /**
     * @test
     */
    public function testBasicURI(): void
    {
        $this->assertResult('<a href="http://www.google.com/">Google</a>');
    }

    /**
     * @test
     */
    public function testInvalidURI(): void
    {
        $this->assertResult(
            '<a href="javascript:badstuff();">Google</a>',
            '<a>Google</a>'
        );
    }

    /**
     * @test
     */
    public function testBdoAddMissingDir(): void
    {
        $this->assertResult(
            '<bdo>Go left.</bdo>',
            '<bdo dir="ltr">Go left.</bdo>'
        );
    }

    /**
     * @test
     */
    public function testBdoReplaceInvalidDirWithDefault(): void
    {
        $this->assertResult(
            '<bdo dir="blahblah">Invalid value!</bdo>',
            '<bdo dir="ltr">Invalid value!</bdo>'
        );
    }

    /**
     * @test
     */
    public function testBdoAlternateDefaultDir(): void
    {
        $this->config->set('Attr.DefaultTextDir', 'rtl');
        $this->assertResult(
            '<bdo>Go right.</bdo>',
            '<bdo dir="rtl">Go right.</bdo>'
        );
    }

    /**
     * @test
     */
    public function testRemoveDirWhenNotRequired(): void
    {
        $this->assertResult(
            '<span dir="blahblah">Invalid value!</span>',
            '<span>Invalid value!</span>'
        );
    }

    /**
     * @test
     */
    public function testTableAttributes(): void
    {
        $this->assertResult(
            '<table frame="above" rules="rows" summary="A test table" border="2" cellpadding="5%" cellspacing="3" width="100%">
    <col align="right" width="4*" />
    <col charoff="5" align="char" width="*" />
    <tr valign="top">
        <th abbr="name">Fiddly name</th>
        <th abbr="price">Super-duper-price</th>
    </tr>
    <tr>
        <td abbr="carrot">Carrot Humungous</td>
        <td>$500.23</td>
    </tr>
    <tr>
        <td colspan="2">Taken off the market</td>
    </tr>
</table>'
        );
    }

    /**
     * @test
     */
    public function testColSpanIsNonZero(): void
    {
        $this->assertResult(
            '<col span="0" />',
            '<col />'
        );
    }

    /**
     * @test
     */
    public function testImgAddDefaults(): void
    {
        $this->config->set('Core.RemoveInvalidImg', false);
        $this->assertResult(
            '<img />',
            '<img src="" alt="Invalid image" />'
        );
    }

    /**
     * @test
     */
    public function testImgGenerateAlt(): void
    {
        $this->assertResult(
            '<img src="foobar.jpg" />',
            '<img src="foobar.jpg" alt="foobar.jpg" />'
        );
    }

    /**
     * @test
     */
    public function testImgAddDefaultSrc(): void
    {
        $this->config->set('Core.RemoveInvalidImg', false);
        $this->assertResult(
            '<img alt="pretty picture" />',
            '<img alt="pretty picture" src="" />'
        );
    }

    /**
     * @test
     */
    public function testImgRemoveNonRetrievableProtocol(): void
    {
        $this->config->set('Core.RemoveInvalidImg', false);
        $this->assertResult(
            '<img src="mailto:foo@example.com" />',
            '<img alt="mailto:foo@example.com" src="" />'
        );
    }

    /**
     * @test
     */
    public function testPreserveRel(): void
    {
        $this->config->set('Attr.AllowedRel', 'nofollow');
        $this->assertResult('<a href="foo" rel="nofollow" />');
    }

    /**
     * @test
     */
    public function testPreserveTarget(): void
    {
        $this->config->set('Attr.AllowedFrameTargets', '_top');
        $this->config->set('HTML.Doctype', 'XHTML 1.0 Transitional');
        $this->assertResult('<a href="foo" target="_top" rel="noreferrer noopener" />');
    }

    /**
     * @test
     */
    public function testRemoveTargetWhenNotSupported(): void
    {
        $this->config->set('HTML.Doctype', 'XHTML 1.0 Strict');
        $this->config->set('Attr.AllowedFrameTargets', '_top');
        $this->assertResult(
            '<a href="foo" target="_top" />',
            '<a href="foo" />'
        );
    }

    /**
     * @test
     */
    public function testKeepAbsoluteCSSWidthAndHeightOnImg(): void
    {
        $this->assertResult(
            '<img src="" alt="" style="width:10px;height:10px;border:1px solid #000;" />'
        );
    }

    /**
     * @test
     */
    public function testRemoveLargeCSSWidthAndHeightOnImg(): void
    {
        $this->assertResult(
            '<img src="" alt="" style="width:10000000px;height:10000000px;border:1px solid #000;" />',
            '<img src="" alt="" style="border:1px solid #000;" />'
        );
    }

    /**
     * @test
     */
    public function testRemoveLargeCSSWidthAndHeightOnImgWithUserConf(): void
    {
        $this->config->set('CSS.MaxImgLength', '1px');
        $this->assertResult(
            '<img src="" alt="" style="width:1mm;height:1mm;border:1px solid #000;" />',
            '<img src="" alt="" style="border:1px solid #000;" />'
        );
    }

    /**
     * @test
     */
    public function testKeepLargeCSSWidthAndHeightOnImgWhenToldTo(): void
    {
        $this->config->set('CSS.MaxImgLength', null);
        $this->assertResult(
            '<img src="" alt="" style="width:10000000px;height:10000000px;border:1px solid #000;" />'
        );
    }

    /**
     * @test
     */
    public function testKeepPercentCSSWidthAndHeightOnImgWhenToldTo(): void
    {
        $this->config->set('CSS.MaxImgLength', null);
        $this->assertResult(
            '<img src="" alt="" style="width:100%;height:100%;border:1px solid #000;" />'
        );
    }

    /**
     * @test
     */
    public function testRemoveRelativeCSSWidthAndHeightOnImg(): void
    {
        $this->assertResult(
            '<img src="" alt="" style="width:10em;height:10em;border:1px solid #000;" />',
            '<img src="" alt="" style="border:1px solid #000;" />'
        );
    }

    /**
     * @test
     */
    public function testRemovePercentCSSWidthAndHeightOnImg(): void
    {
        $this->assertResult(
            '<img src="" alt="" style="width:100%;height:100%;border:1px solid #000;" />',
            '<img src="" alt="" style="border:1px solid #000;" />'
        );
    }
}
