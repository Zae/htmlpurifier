<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\DefinitionCache;

use HTMLPurifier_Config;
use HTMLPurifier_Definition;
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
     * @return HTMLPurifier_Config|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    protected function generateConfigMock(string $serial = 'defaultserial')
    {
        $config = Mockery::mock(HTMLPurifier_Config::class);

        $config->shouldReceive('getBatchSerial')
            ->withArgs(['Test'])
            ->andReturn($serial);

        $config->shouldReceive('get')
            ->andReturnNull();

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
        $def = Mockery::mock(HTMLPurifier_Definition::class)->makePartial();

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
}
