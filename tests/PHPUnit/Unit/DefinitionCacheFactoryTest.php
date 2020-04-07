<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier_DefinitionCache_Decorator_Memory;
use HTMLPurifier_DefinitionCache_Null;
use HTMLPurifier_DefinitionCache_Serializer;
use HTMLPurifier_DefinitionCacheFactory;
use Mockery;

/**
 * Class DefinitionCacheFactoryTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class DefinitionCacheFactoryTest extends TestCase
{
    /**
     * @var HTMLPurifier_DefinitionCacheFactory
     */
    private $factory;

    /**
     * @var HTMLPurifier_DefinitionCacheFactory
     */
    private $oldFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->factory = new HTMLPurifier_DefinitionCacheFactory();
        $this->oldFactory = HTMLPurifier_DefinitionCacheFactory::instance();
        HTMLPurifier_DefinitionCacheFactory::instance($this->factory);
    }

    public function tearDown(): void
    {
        HTMLPurifier_DefinitionCacheFactory::instance($this->oldFactory);
        parent::tearDown();
    }

    /**
     * @test
     * @throws \HTMLPurifier_Exception
     */
    public function test_create(): void
    {
        $cache = $this->factory->create('Test', $this->config);
        static::assertEquals($cache, new HTMLPurifier_DefinitionCache_Serializer('Test'));
    }

    /**
     * @test
     * @throws \HTMLPurifier_Exception
     */
    public function test_create_withDecorator(): void
    {
        $this->factory->addDecorator('Memory');
        $cache = $this->factory->create('Test', $this->config);
        $cache_real = new HTMLPurifier_DefinitionCache_Decorator_Memory();
        $ser = new HTMLPurifier_DefinitionCache_Serializer('Test');
        $cache_real = $cache_real->decorate($ser);

        static::assertEquals($cache, $cache_real);
    }

    /**
     * @test
     * @throws \HTMLPurifier_Exception
     */
    public function test_create_withDecoratorObject(): void
    {
        $this->factory->addDecorator(new HTMLPurifier_DefinitionCache_Decorator_Memory());
        $cache = $this->factory->create('Test', $this->config);
        $cache_real = new HTMLPurifier_DefinitionCache_Decorator_Memory();
        $ser = new HTMLPurifier_DefinitionCache_Serializer('Test');
        $cache_real = $cache_real->decorate($ser);

        static::assertEquals($cache, $cache_real);
    }

    /**
     * @test
     * @throws \HTMLPurifier_Exception
     */
    public function test_create_recycling(): void
    {
        $cache  = $this->factory->create('Test', $this->config);
        $cache2 = $this->factory->create('Test', $this->config);

        static::assertEquals($cache, $cache2);
    }

    /**
     * @test
     * @throws \HTMLPurifier_Exception
     */
    public function test_create_invalid(): void
    {
        $this->config->set('Cache.DefinitionImpl', 'Invalid');

        $this->expectError();
        $this->expectErrorMessage('Unrecognized DefinitionCache Invalid, using Serializer instead');

        $cache = $this->factory->create('Test', $this->config);

        static::assertInstanceOf( HTMLPurifier_DefinitionCache_Serializer::class, $cache);
    }

    /**
     * @test
     * @throws \HTMLPurifier_Exception
     */
    public function test_null(): void
    {
        $this->config->set('Cache.DefinitionImpl', null);
        $cache = $this->factory->create('Test', $this->config);

        static::assertEquals($cache, new HTMLPurifier_DefinitionCache_Null('Test'));
    }

    /**
     * @test
     * @throws \HTMLPurifier_Exception
     */
    public function test_register(): void
    {
        static::markTestSkipped('Don\'t know how to register our mock in the factory yet...');

        $externalMock = Mockery::mock('overload:HTMLPurifier_DefinitionCache');
        $this->config->set('Cache.DefinitionImpl', 'TestCache');
        $this->factory->register('TestCache', $class = 'HTMLPurifier_DefinitionCacheMock');
        $cache = $this->factory->create('Test', $this->config);

        static::assertInstanceOf($class, $cache);
    }
}
