<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier_DefinitionCache_Decorator_Memory;
use HTMLPurifier\DefinitionCache\DevNull;
use HTMLPurifier\DefinitionCache\Serializer;
use HTMLPurifier\DefinitionCacheFactory;
use Mockery;

/**
 * Class DefinitionCacheFactoryTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class DefinitionCacheFactoryTest extends TestCase
{
    /**
     * @var \HTMLPurifier\DefinitionCacheFactory
     */
    private $factory;

    /**
     * @var DefinitionCacheFactory
     */
    private $oldFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->factory = new DefinitionCacheFactory();
        $this->oldFactory = DefinitionCacheFactory::instance();
        DefinitionCacheFactory::instance($this->factory);
    }

    public function tearDown(): void
    {
        DefinitionCacheFactory::instance($this->oldFactory);
        parent::tearDown();
    }

    /**
     * @test
     * @throws \HTMLPurifier\Exception
     */
    public function test_create(): void
    {
        $cache = $this->factory->create('Test', $this->config);
        static::assertEquals($cache, new Serializer('Test'));
    }

    /**
     * @test
     * @throws \HTMLPurifier\Exception
     */
    public function test_create_withDecorator(): void
    {
        $this->factory->addDecorator('Memory');
        $cache = $this->factory->create('Test', $this->config);
        $cache_real = new HTMLPurifier_DefinitionCache_Decorator_Memory();
        $ser = new Serializer('Test');
        $cache_real = $cache_real->decorate($ser);

        static::assertEquals($cache, $cache_real);
    }

    /**
     * @test
     * @throws \HTMLPurifier\Exception
     */
    public function test_create_withDecoratorObject(): void
    {
        $this->factory->addDecorator(new HTMLPurifier_DefinitionCache_Decorator_Memory());
        $cache = $this->factory->create('Test', $this->config);
        $cache_real = new HTMLPurifier_DefinitionCache_Decorator_Memory();
        $ser = new Serializer('Test');
        $cache_real = $cache_real->decorate($ser);

        static::assertEquals($cache, $cache_real);
    }

    /**
     * @test
     * @throws \HTMLPurifier\Exception
     */
    public function test_create_recycling(): void
    {
        $cache  = $this->factory->create('Test', $this->config);
        $cache2 = $this->factory->create('Test', $this->config);

        static::assertEquals($cache, $cache2);
    }

    /**
     * @test
     * @throws \HTMLPurifier\Exception
     */
    public function test_create_invalid(): void
    {
        $this->config->set('Cache.DefinitionImpl', 'Invalid');

        $this->expectError();
        $this->expectErrorMessage('Unrecognized DefinitionCache Invalid, using Serializer instead');

        $cache = $this->factory->create('Test', $this->config);

        static::assertInstanceOf( Serializer::class, $cache);
    }

    /**
     * @test
     * @throws \HTMLPurifier\Exception
     */
    public function test_null(): void
    {
        $this->config->set('Cache.DefinitionImpl', null);
        $cache = $this->factory->create('Test', $this->config);

        static::assertEquals($cache, new DevNull('Test'));
    }

    /**
     * @test
     * @throws \HTMLPurifier\Exception
     */
    public function test_register(): void
    {
        static::markTestSkipped('Don\'t know how to register our mock in the factory yet...');

        $externalMock = Mockery::mock('overload:HTMLPurifier\HTMLPurifier_DefinitionCache');
        $this->config->set('Cache.DefinitionImpl', 'TestCache');
        $this->factory->register('TestCache', $class = 'HTMLPurifier_DefinitionCacheMock');
        $cache = $this->factory->create('Test', $this->config);

        static::assertInstanceOf($class, $cache);
    }
}
