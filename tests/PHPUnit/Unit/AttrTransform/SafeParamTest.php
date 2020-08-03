<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrTransform;

use HTMLPurifier\AttrTransform\SafeParam;

/**
 * Class SafeParamTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrTransform
 */
class SafeParamTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new SafeParam();
    }

    /**
     * @test
     */
    public function testAllowFullScreenDisabled(): void
    {
        $this->config->set('HTML.FlashAllowFullScreen', false);
        $attr = ['name' => 'allowFullScreen', 'value' => true];

        $transformedAttr = $this->obj->transform($attr, $this->config, $this->context);

        static::assertEquals(['name' => 'allowFullScreen', 'value' => 'false'], $transformedAttr);
    }

    /**
     * @test
     */
    public function testNull(): void
    {
        $attr = ['name' => 'doesnotexist', 'value' => true];

        $transformedAttr = $this->obj->transform($attr, $this->config, $this->context);

        static::assertEquals(['name' => null, 'value' => null], $transformedAttr);
    }
}
