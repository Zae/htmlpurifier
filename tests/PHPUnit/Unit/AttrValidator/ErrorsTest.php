<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrValidator;

use HTMLPurifier_AttrTransform;
use HTMLPurifier_AttrValidator;
use HTMLPurifier_Config;
use HTMLPurifier_ErrorCollector;
use HTMLPurifier_Generator;
use HTMLPurifier_LanguageFactory;
use HTMLPurifier_Token_Start;
use Mockery;

/**
 * Class ErrorsTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrValidator
 */
class ErrorsTest extends \HTMLPurifier\Tests\Unit\ErrorsTest
{
    private $language;

    public function setUp(): void
    {
        parent::setUp();

        $config = HTMLPurifier_Config::createDefault();
        $this->language = HTMLPurifier_LanguageFactory::instance()->create($config, $this->context);
        $this->context->register('Locale', $this->language);
        $this->collector = new HTMLPurifier_ErrorCollector($this->context);
        $gen = new HTMLPurifier_Generator($config, $this->context);
        $this->context->register('Generator', $gen);
    }

    /**
     * @test
     */
    public function testAttributesTransformedGlobalPre(): void
    {
        $def = $this->config->getHTMLDefinition(true);

        $transform = Mockery::mock(HTMLPurifier_AttrTransform::class);

        $input = ['original' => 'value'];
        $output = ['class' => 'value']; // must be valid

        $transform->expects()
            ->transform($input, Mockery::any(), Mockery::any())
            ->andReturn($output);

        $def->info_attr_transform_pre[] = $transform;

        $token = new HTMLPurifier_Token_Start('span', $input, 1);
        $this->invoke($token);

        $result = $this->collector->getRaw();
        $expect = [
            [1, E_NOTICE, 'Attributes on <span> transformed from original to class', []],
        ];

        static::assertEquals($result, $expect);
    }

    /**
     * @test
     */
    public function testAttributesTransformedLocalPre(): void
    {
        $this->config->set('HTML.TidyLevel', 'heavy');
        $input = ['align' => 'right'];

        $token = new HTMLPurifier_Token_Start('p', $input, 1);
        $this->invoke($token);
        $result = $this->collector->getRaw();
        $expect = [
            [1, E_NOTICE, 'Attributes on <p> transformed from align to style', []],
        ];

        static::assertEquals($result, $expect);
    }

    // too lazy to check for global post and global pre

    /**
     * too lazy to check for global post and global pre
     * @test
     */
    public function testAttributeRemoved(): void
    {
        $token = new HTMLPurifier_Token_Start('p', ['foobar' => 'right'], 1);
        $this->invoke($token);
        $result = $this->collector->getRaw();
        $expect = [
            [1, E_ERROR, 'foobar attribute on <p> removed', []],
        ];

        static::assertEquals($result, $expect);
    }

    /**
     * @param $input
     */
    private function invoke($input): void
    {
        $validator = new HTMLPurifier_AttrValidator();
        $validator->validateToken($input, $this->config, $this->context);
    }
}
