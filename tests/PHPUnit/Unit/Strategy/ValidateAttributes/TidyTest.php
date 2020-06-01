<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Strategy\ValidateAttributes;

use HTMLPurifier\Strategy\ValidateAttributes;
use HTMLPurifier\Tests\Unit\Strategy\TestCase;

/**
 * Class TidyTest
 *
 * @package HTMLPurifier\Tests\Unit\Strategy\ValidateAttributes
 */
class TidyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new ValidateAttributes();
        $this->config->set('HTML.TidyLevel', 'heavy');
    }

    /**
     * @test
     */
    public function testConvertCenterAlign(): void
    {
        $this->assertResult(
            '<h1 align="center">Centered Headline</h1>',
            '<h1 style="text-align:center;">Centered Headline</h1>'
        );
    }

    /**
     * @test
     */
    public function testConvertRightAlign(): void
    {
        $this->assertResult(
            '<h1 align="right">Right-aligned Headline</h1>',
            '<h1 style="text-align:right;">Right-aligned Headline</h1>'
        );
    }

    /**
     * @test
     */
    public function testConvertLeftAlign(): void
    {
        $this->assertResult(
            '<h1 align="left">Left-aligned Headline</h1>',
            '<h1 style="text-align:left;">Left-aligned Headline</h1>'
        );
    }

    /**
     * @test
     */
    public function testConvertJustifyAlign(): void
    {
        $this->assertResult(
            '<p align="justify">Justified Paragraph</p>',
            '<p style="text-align:justify;">Justified Paragraph</p>'
        );
    }

    /**
     * @test
     */
    public function testRemoveInvalidAlign(): void
    {
        $this->assertResult(
            '<h1 align="invalid">Invalid Headline</h1>',
            '<h1>Invalid Headline</h1>'
        );
    }

    /**
     * @test
     */
    public function testConvertTableLengths(): void
    {
        $this->assertResult(
            '<td width="5%" height="10" /><th width="10" height="5%" /><hr width="10" height="10" />',
            '<td style="width:5%;height:10px;" /><th style="width:10px;height:5%;" /><hr style="width:10px;" />'
        );
    }

    /**
     * @test
     */
    public function testTdConvertNowrap(): void
    {
        $this->assertResult(
            '<td nowrap />',
            '<td style="white-space:nowrap;" />'
        );
    }

    /**
     * @test
     */
    public function testCaptionConvertAlignLeft(): void
    {
        $this->assertResult(
            '<caption align="left" />',
            '<caption style="text-align:left;" />'
        );
    }

    /**
     * @test
     */
    public function testCaptionConvertAlignRight(): void
    {
        $this->assertResult(
            '<caption align="right" />',
            '<caption style="text-align:right;" />'
        );
    }

    /**
     * @test
     */
    public function testCaptionConvertAlignTop(): void
    {
        $this->assertResult(
            '<caption align="top" />',
            '<caption style="caption-side:top;" />'
        );
    }

    /**
     * @test
     */
    public function testCaptionConvertAlignBottom(): void
    {
        $this->assertResult(
            '<caption align="bottom" />',
            '<caption style="caption-side:bottom;" />'
        );
    }

    /**
     * @test
     */
    public function testCaptionRemoveInvalidAlign(): void
    {
        $this->assertResult(
            '<caption align="nonsense" />',
            '<caption />'
        );
    }

    /**
     * @test
     */
    public function testTableConvertAlignLeft(): void
    {
        $this->assertResult(
            '<table align="left" />',
            '<table style="float:left;" />'
        );
    }

    /**
     * @test
     */
    public function testTableConvertAlignCenter(): void
    {
        $this->assertResult(
            '<table align="center" />',
            '<table style="margin-left:auto;margin-right:auto;" />'
        );
    }

    /**
     * @test
     */
    public function testTableConvertAlignRight(): void
    {
        $this->assertResult(
            '<table align="right" />',
            '<table style="float:right;" />'
        );
    }

    /**
     * @test
     */
    public function testTableRemoveInvalidAlign(): void
    {
        $this->assertResult(
            '<table align="top" />',
            '<table />'
        );
    }

    /**
     * @test
     */
    public function testImgConvertAlignLeft(): void
    {
        $this->assertResult(
            '<img src="foobar.jpg" alt="foobar" align="left" />',
            '<img src="foobar.jpg" alt="foobar" style="float:left;" />'
        );
    }

    /**
     * @test
     */
    public function testImgConvertAlignRight(): void
    {
        $this->assertResult(
            '<img src="foobar.jpg" alt="foobar" align="right" />',
            '<img src="foobar.jpg" alt="foobar" style="float:right;" />'
        );
    }

    /**
     * @test
     */
    public function testImgConvertAlignBottom(): void
    {
        $this->assertResult(
            '<img src="foobar.jpg" alt="foobar" align="bottom" />',
            '<img src="foobar.jpg" alt="foobar" style="vertical-align:baseline;" />'
        );
    }

    /**
     * @test
     */
    public function testImgConvertAlignMiddle(): void
    {
        $this->assertResult(
            '<img src="foobar.jpg" alt="foobar" align="middle" />',
            '<img src="foobar.jpg" alt="foobar" style="vertical-align:middle;" />'
        );
    }

    /**
     * @test
     */
    public function testImgConvertAlignTop(): void
    {
        $this->assertResult(
            '<img src="foobar.jpg" alt="foobar" align="top" />',
            '<img src="foobar.jpg" alt="foobar" style="vertical-align:top;" />'
        );
    }

    /**
     * @test
     */
    public function testImgRemoveInvalidAlign(): void
    {
        $this->assertResult(
            '<img src="foobar.jpg" alt="foobar" align="outerspace" />',
            '<img src="foobar.jpg" alt="foobar" />'
        );
    }

    /**
     * @test
     */
    public function testBorderConvertHVSpace(): void
    {
        $this->assertResult(
            '<img src="foo" alt="foo" hspace="1" vspace="3" />',
            '<img src="foo" alt="foo" style="margin-top:3px;margin-bottom:3px;margin-left:1px;margin-right:1px;" />'
        );
    }

    /**
     * @test
     */
    public function testHrConvertSize(): void
    {
        $this->assertResult(
            '<hr size="3" />',
            '<hr style="height:3px;" />'
        );
    }

    /**
     * @test
     */
    public function testHrConvertNoshade(): void
    {
        $this->assertResult(
            '<hr noshade />',
            '<hr style="color:#808080;background-color:#808080;border:0;" />'
        );
    }

    /**
     * @test
     */
    public function testHrConvertAlignLeft(): void
    {
        $this->assertResult(
            '<hr align="left" />',
            '<hr style="margin-left:0;margin-right:auto;text-align:left;" />'
        );
    }

    /**
     * @test
     */
    public function testHrConvertAlignCenter(): void
    {
        $this->assertResult(
            '<hr align="center" />',
            '<hr style="margin-left:auto;margin-right:auto;text-align:center;" />'
        );
    }

    /**
     * @test
     */
    public function testHrConvertAlignRight(): void
    {
        $this->assertResult(
            '<hr align="right" />',
            '<hr style="margin-left:auto;margin-right:0;text-align:right;" />'
        );
    }

    /**
     * @test
     */
    public function testHrRemoveInvalidAlign(): void
    {
        $this->assertResult(
            '<hr align="bottom" />',
            '<hr />'
        );
    }

    /**
     * @test
     */
    public function testBrConvertClearLeft(): void
    {
        $this->assertResult(
            '<br clear="left" />',
            '<br style="clear:left;" />'
        );
    }

    /**
     * @test
     */
    public function testBrConvertClearRight(): void
    {
        $this->assertResult(
            '<br clear="right" />',
            '<br style="clear:right;" />'
        );
    }

    /**
     * @test
     */
    public function testBrConvertClearAll(): void
    {
        $this->assertResult(
            '<br clear="all" />',
            '<br style="clear:both;" />'
        );
    }

    /**
     * @test
     */
    public function testBrConvertClearNone(): void
    {
        $this->assertResult(
            '<br clear="none" />',
            '<br style="clear:none;" />'
        );
    }

    /**
     * @test
     */
    public function testBrRemoveInvalidClear(): void
    {
        $this->assertResult(
            '<br clear="foo" />',
            '<br />'
        );
    }

    /**
     * @test
     */
    public function testUlConvertTypeDisc(): void
    {
        $this->assertResult(
            '<ul type="disc" />',
            '<ul style="list-style-type:disc;" />'
        );
    }

    /**
     * @test
     */
    public function testUlConvertTypeSquare(): void
    {
        $this->assertResult(
            '<ul type="square" />',
            '<ul style="list-style-type:square;" />'
        );
    }

    /**
     * @test
     */
    public function testUlConvertTypeCircle(): void
    {
        $this->assertResult(
            '<ul type="circle" />',
            '<ul style="list-style-type:circle;" />'
        );
    }

    /**
     * @test
     */
    public function testUlConvertTypeCaseInsensitive(): void
    {
        $this->assertResult(
            '<ul type="CIRCLE" />',
            '<ul style="list-style-type:circle;" />'
        );
    }

    /**
     * @test
     */
    public function testUlRemoveInvalidType(): void
    {
        $this->assertResult(
            '<ul type="a" />',
            '<ul />'
        );
    }

    /**
     * @test
     */
    public function testOlConvertType1(): void
    {
        $this->assertResult(
            '<ol type="1" />',
            '<ol style="list-style-type:decimal;" />'
        );
    }

    /**
     * @test
     */
    public function testOlConvertTypeLowerI(): void
    {
        $this->assertResult(
            '<ol type="i" />',
            '<ol style="list-style-type:lower-roman;" />'
        );
    }

    /**
     * @test
     */
    public function testOlConvertTypeUpperI(): void
    {
        $this->assertResult(
            '<ol type="I" />',
            '<ol style="list-style-type:upper-roman;" />'
        );
    }

    /**
     * @test
     */
    public function testOlConvertTypeLowerA(): void
    {
        $this->assertResult(
            '<ol type="a" />',
            '<ol style="list-style-type:lower-alpha;" />'
        );
    }

    /**
     * @test
     */
    public function testOlConvertTypeUpperA(): void
    {
        $this->assertResult(
            '<ol type="A" />',
            '<ol style="list-style-type:upper-alpha;" />'
        );
    }

    /**
     * @test
     */
    public function testOlRemoveInvalidType(): void
    {
        $this->assertResult(
            '<ol type="disc" />',
            '<ol />'
        );
    }

    /**
     * @test
     */
    public function testLiConvertTypeCircle(): void
    {
        $this->assertResult(
            '<li type="circle" />',
            '<li style="list-style-type:circle;" />'
        );
    }

    /**
     * @test
     */
    public function testLiConvertTypeA(): void
    {
        $this->assertResult(
            '<li type="A" />',
            '<li style="list-style-type:upper-alpha;" />'
        );
    }

    /**
     * @test
     */
    public function testLiConvertTypeCaseSensitive(): void
    {
        $this->assertResult(
            '<li type="CIRCLE" />',
            '<li />'
        );
    }
}
