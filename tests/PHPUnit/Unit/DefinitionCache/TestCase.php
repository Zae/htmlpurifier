<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\DefinitionCache;

use HTMLPurifier\Config;
use HTMLPurifier\Definition;
use Mockery;
use Mockery\Mock;

/**
 * Class TestCase
 *
 * @package HTMLPurifier\Tests\Unit\DefinitionCache
 */
abstract class TestCase extends \HTMLPurifier\Tests\Unit\TestCase
{
    /**
     * Generate a configuration mock object that returns $values
     * to a getBatch() call
     *
     * @param string $serial
     *
     * @return Config|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    protected function generateConfigMock(string $serial = 'defaultserial')
    {
        $config = Mockery::mock(Config::class);

        $config->shouldReceive('getBatchSerial')
            ->withArgs(['Test'])
            ->andReturn($serial);

        $config->shouldReceive('get')
            ->zeroOrMoreTimes()
            ->andReturnNull()
            ->byDefault();

        $config->version = '1.0.0';
        return $config;
    }

    protected function getConfigMock(string $serial = 'defaultserial')
    {
        $config = Mockery::mock(Config::class);

        $config->shouldReceive('getBatchSerial')
               ->withArgs(['Test'])
               ->andReturn($serial);

        $config->version = '1.0.0';
        return $config;
    }

    /**
     * Returns an anonymous def that has been setup and named Test
     *
     * @param array $member_vars
     *
     * @return Mock
     */
    protected function generateDefinition(array $member_vars = [])
    {
        $def = Mockery::mock(Definition::class)->makePartial();

        $def->shouldAllowMockingProtectedMethods();
        $def->expects()
            ->doSetup(Mockery::any())
            ->times(0)
            ->andReturn(true);

        $def->setup = true;
        $def->type  = 'Test';
        foreach ($member_vars as $key => $val) {
            $def->$key = $val;
        }

        return $def;
    }

    /**
     * Returns an anonymous def that has been setup and named Test
     *
     * @param array $member_vars
     *
     * @return Definition
     */
    protected function generateSerializableDefinition(array $member_vars = [])
    {
        $def = new TestDefinition();

        foreach ($member_vars as $key => $val) {
            $def->$key = $val;
        }

        return $def;
    }
}

class TestDefinition extends Definition
{
    public $type = 'Test';
    public $setup = true;

    /**
     * Sets up the definition object into the final form, something
     * not done by the constructor
     *
     * @param Config $config
     */
    protected function doSetup(Config $config): void
    {
        // do nothing...
    }
}
