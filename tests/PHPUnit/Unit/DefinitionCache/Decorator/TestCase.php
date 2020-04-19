<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\DefinitionCache\Decorator;

use HTMLPurifier\DefinitionCache;
use Mockery;

/**
 * Class TestCase
 *
 * @package HTMLPurifier\Tests\Unit\DefinitionCache\Decorator
 */
abstract class TestCase extends \HTMLPurifier\Tests\Unit\DefinitionCache\TestCase
{
    /**
     * @var \HTMLPurifier\DefinitionCache|\Mockery\LegacyMockInterface|\Mockery\MockInterface
     */
    protected $mock;

    protected $def;
    protected $config;
    protected $cache;

    public function setUp(): void
    {
        $this->mock = Mockery::mock(DefinitionCache::class);

        $this->mock->type = 'Test';
        $this->cache    = $this->cache->decorate($this->mock);
        $this->def      = $this->generateDefinition();
        $this->config   = $this->generateConfigMock();
    }

    public function tearDown(): void
    {
        unset($this->mock, $this->cache);

        parent::tearDown();
    }
}
