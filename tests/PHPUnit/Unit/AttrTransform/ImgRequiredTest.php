<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrTransform;

use HTMLPurifier\AttrTransform\ImgRequired;

/**
 * Class ImgRequiredTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrTransform
 */
class ImgRequiredTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new ImgRequired();
    }

    /**
     * @test
     */
    public function testAddMissingAttr(): void
    {
        $this->config->set('Core.RemoveInvalidImg', false);
        $this->assertResult(
            [],
            ['src' => '', 'alt' => 'Invalid image']
        );
    }

    /**
     * @test
     */
    public function testAlternateDefaults(): void
    {
        $this->config->set('Attr.DefaultInvalidImage', 'blank.png');
        $this->config->set('Attr.DefaultInvalidImageAlt', 'Pawned!');
        $this->config->set('Attr.DefaultImageAlt', 'not pawned');
        $this->config->set('Core.RemoveInvalidImg', false);
        $this->assertResult(
            [],
            ['src' => 'blank.png', 'alt' => 'Pawned!']
        );
    }

    /**
     * @test
     */
    public function testGenerateAlt(): void
    {
        $this->assertResult(
            ['src' => '/path/to/foobar.png'],
            ['src' => '/path/to/foobar.png', 'alt' => 'foobar.png']
        );
    }

    /**
     * @test
     */
    public function testAddDefaultSrc(): void
    {
        $this->config->set('Core.RemoveInvalidImg', false);
        $this->assertResult(
            ['alt' => 'intrigue'],
            ['alt' => 'intrigue', 'src' => '']
        );
    }

    /**
     * @test
     */
    public function testAddDefaultAlt(): void
    {
        $this->config->set('Attr.DefaultImageAlt', 'default');
        $this->assertResult(
            ['src' => ''],
            ['src' => '', 'alt' => 'default']
        );
    }
}
