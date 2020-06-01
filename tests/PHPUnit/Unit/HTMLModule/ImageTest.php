<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\HTMLModule;

/**
 * Class ImageTest
 *
 * @package HTMLPurifier\Tests\Unit\HTMLModule
 */
class ImageTest extends TestCase
{
    /**
     * @test
     */
    public function testNormal(): void
    {
        $this->assertResult('<img height="40" width="40" src="" alt="" />');
    }

    /**
     * @test
     */
    public function testLengthTooLarge(): void
    {
        $this->assertResult(
            '<img height="40000" width="40000" src="" alt="" />',
            '<img height="1200" width="1200" src="" alt="" />'
        );
    }

    /**
     * @test
     */
    public function testLengthPercentage(): void
    {
        $this->assertResult(
            '<img height="100%" width="100%" src="" alt="" />',
            '<img src="" alt="" />'
        );
    }

    /**
     * @test
     */
    public function testLengthCustomMax(): void
    {
        $this->config->set('HTML.MaxImgLength', 20);
        $this->assertResult(
            '<img height="30" width="30" src="" alt="" />',
            '<img height="20" width="20" src="" alt="" />'
        );
    }

    /**
     * @test
     */
    public function testLengthCrashFixDisabled(): void
    {
        $this->config->set('HTML.MaxImgLength', null);
        $this->assertResult('<img height="100%" width="100%" src="" alt="" />');
        $this->assertResult('<img height="40000" width="40000" src="" alt="" />');
    }

    /**
     * @test
     */
    public function testLengthTrusted(): void
    {
        $this->config->set('HTML.Trusted', true);
        $this->assertResult('<img height="100%" width="100%" src="" alt="" />');
        $this->assertResult('<img height="40000" width="40000" src="" alt="" />');
    }
}
