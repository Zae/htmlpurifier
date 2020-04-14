<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\DefinitionCache;

use HTMLPurifier_DefinitionCache;
use HTMLPurifier\DefinitionCache\Decorator;
use Mockery;

/**
 * Class DecoratorTest
 *
 * @package HTMLPurifier\Tests\Unit\DefinitionCache
 */
class DecoratorTest extends TestCase
{
    /**
     * @test
     */
    public function testIt(): void
    {
        $mock = Mockery::mock(HTMLPurifier_DefinitionCache::class);
        $mock->type = 'Test';

        $cache = new Decorator();
        $cache = $cache->decorate($mock);

        static::assertEquals($cache->type, $mock->type);

        $def = $this->generateDefinition();
        $config = $this->generateConfigMock();

        $mock->expects()
            ->add($def, $config)
            ->once();

        $cache->add($def, $config);

        $mock->expects()
            ->set($def, $config)
            ->once();

        $cache->set($def, $config);

        $mock->expects()
            ->replace($def, $config)
            ->once();

        $cache->replace($def, $config);

        $mock->expects()
            ->get($config)
            ->once();

        $cache->get($config);

        $mock->expects()
            ->flush($config)
            ->once();

        $cache->flush($config);

        $mock->expects()
            ->cleanup($config)
            ->once();

        $cache->cleanup($config);
    }
}
