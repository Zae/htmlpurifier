<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\Config;
use HTMLPurifier\Context;
use HTMLPurifier\TagTransform;
use HTMLPurifier\TagTransform\Font;
use HTMLPurifier\TagTransform\Simple;
use HTMLPurifier\Token\EmptyToken;
use HTMLPurifier\Token\End;
use HTMLPurifier\Token\Start;

/**
 * Class TagTransformTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class TagTransformTest extends TestCase
{
    /**
     * Asserts that a transformation happens
     *
     * This assertion performs several tests on the transform:
     *
     * -# Transforms a start tag with only $name and no attributes
     * -# Transforms a start tag with $name and $attributes
     * -# Transform an end tag
     * -# Transform an empty tag with only $name and no attributes
     * -# Transform an empty tag with $name and $attributes
     *
     * In its current form, it assumes that start and empty tags would be
     * treated the same, and is really ensuring that the tag transform doesn't
     * do anything wonky to the tag type.
     *
     * @param TagTransform  $transformer             class to test
     * @param string        $name                    of the original tag
     * @param array         $attributes              of the original tag
     * @param string        $expect_name             of output tag
     * @param array         $expect_attributes       of output tag when $attribute is included.
     * @param array         $expect_added_attributes of output tag when $attributes are omitted.
     * @param array         $config_array     Configuration array for \HTMLPurifier\Config
     * @param array         $context_array           array for HTMLPurifier\HTMLPurifier_Context
     */
    private function assertTransformation(
        TagTransform $transformer,
        string $name,
        array $attributes,
        string $expect_name,
        array $expect_attributes,
        array $expect_added_attributes = [],
        array $config_array = [],
        array $context_array = []
    ): void {
        $config = Config::createDefault();
        $config->loadArray($config_array);

        $context = new Context();
        $context->loadArray($context_array);

        // start tag transform
        static::assertEquals(
            new Start($expect_name, $expect_added_attributes),
            $transformer->transform(new Start($name), $config, $context)
        );

        // start tag transform with attributes
        static::assertEquals(
            new Start($expect_name, $expect_attributes),
            $transformer->transform(
                new Start($name, $attributes),
                $config, $context
            )
        );

        // end tag transform
        static::assertEquals(
            new End($expect_name),
            $transformer->transform(
                new End($name), $config, $context
            )
        );

        // empty tag transform
        static::assertEquals(
            new EmptyToken($expect_name, $expect_added_attributes),
            $transformer->transform(
                new EmptyToken($name), $config, $context
            )
        );

        // empty tag transform with attributes
        static::assertEquals(
            new EmptyToken($expect_name, $expect_attributes),
            $transformer->transform(
                new EmptyToken($name, $attributes),
                $config, $context
            )
        );
    }

    /**
     * @test
     */
    public function testSimple(): void
    {
        $transformer = new Simple('ul');

        $this->assertTransformation(
            $transformer,
            'menu',
            ['class' => 'boom'],
            'ul',
            ['class' => 'boom']
        );
    }

    /**
     * @test
     */
    public function testSimpleWithCSS(): void
    {
        $transformer = new Simple('div', 'text-align:center;');

        $this->assertTransformation(
            $transformer,
            'center',
            ['class' => 'boom', 'style'=>'font-weight:bold;'],
            'div',
            ['class' => 'boom', 'style'=>'text-align:center;font-weight:bold;'],
            ['style' => 'text-align:center;']
        );

        // test special case, uppercase attribute key
        $this->assertTransformation(
            $transformer,
            'center',
            ['STYLE' => 'font-weight:bold;'],
            'div',
            ['style' => 'text-align:center;font-weight:bold;'],
            ['style' => 'text-align:center;']
        );
    }

    /**
     * @param $transformer
     * @param $size
     * @param $style
     */
    private function assertSizeToStyle($transformer, $size, $style): void
    {
        $this->assertTransformation(
            $transformer,
            'font',
            ['size' => $size],
            'span',
            ['style' => "font-size:{$style};"]
        );
    }

    /**
     * @test
     */
    public function testFont(): void
    {
        $transformer = new Font();

        // test a font-face transformation
        $this->assertTransformation(
            $transformer,
            'font',
            ['face' => 'Arial'],
            'span',
            ['style' => 'font-family:Arial;']
        );

        // test a color transformation
        $this->assertTransformation(
            $transformer,
            'font',
            ['color' => 'red'],
            'span',
            ['style' => 'color:red;']
        );

        // test the size transforms
        $this->assertSizeToStyle($transformer, '0', 'xx-small');
        $this->assertSizeToStyle($transformer, '1', 'xx-small');
        $this->assertSizeToStyle($transformer, '2', 'small');
        $this->assertSizeToStyle($transformer, '3', 'medium');
        $this->assertSizeToStyle($transformer, '4', 'large');
        $this->assertSizeToStyle($transformer, '5', 'x-large');
        $this->assertSizeToStyle($transformer, '6', 'xx-large');
        $this->assertSizeToStyle($transformer, '7', '300%');
        $this->assertSizeToStyle($transformer, '-1', 'smaller');
        $this->assertSizeToStyle($transformer, '-2', '60%');
        $this->assertSizeToStyle($transformer, '-3', '60%');
        $this->assertSizeToStyle($transformer, '+1', 'larger');
        $this->assertSizeToStyle($transformer, '+2', '150%');
        $this->assertSizeToStyle($transformer, '+3', '200%');
        $this->assertSizeToStyle($transformer, '+4', '300%');
        $this->assertSizeToStyle($transformer, '+5', '300%');
        $this->assertTransformation(
            $transformer,
            'font',
            ['size' => ''],
            'span',
            []
        );

        // test multiple transforms, the alphabetical ordering is important
        $this->assertTransformation(
            $transformer,
            'font',
            ['color' => 'red', 'face' => 'Arial', 'size' => '6'],
            'span',
            ['style' => 'color:red;font-family:Arial;font-size:xx-large;']
        );
    }
}
