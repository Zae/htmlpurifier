<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrValidator;

use HTMLPurifier\AttrTransform;
use HTMLPurifier\AttrValidator;
use \HTMLPurifier\Config;
use HTMLPurifier\ErrorCollector;
use HTMLPurifier\Generator;
use HTMLPurifier\LanguageFactory;
use HTMLPurifier\Token\Start;
use Mockery;

/**
 * Class ErrorsTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrValidator
 */
abstract class ErrorsTestCase extends \HTMLPurifier\Tests\Unit\ErrorsTestCase
{
    private $language;

    protected function setUp(): void
    {
        parent::setUp();

        $config = \HTMLPurifier\Config::createDefault();
        $this->language = LanguageFactory::instance()->create($config, $this->context);
        $this->context->register('Locale', $this->language);
        $this->collector = new ErrorCollector($this->context);
        $gen = new Generator($config, $this->context);
        $this->context->register('Generator', $gen);
    }

    /**
     * @test
     */
    public function testAttributesTransformedGlobalPre(): void
    {
        $def = $this->config->getHTMLDefinition(true);

        $transform = Mockery::mock(AttrTransform::class);

        $input = ['original' => 'value'];
        $output = ['class' => 'value']; // must be valid

        $transform->expects()
            ->transform($input, Mockery::any(), Mockery::any())
            ->andReturn($output);

        $def->info_attr_transform_pre[] = $transform;

        $token = new Start('span', $input, 1);
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

        $token = new Start('p', $input, 1);
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
        $token = new Start('p', ['foobar' => 'right'], 1);
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
        $validator = new AttrValidator();
        $validator->validateToken($input, $this->config, $this->context);
    }
}
