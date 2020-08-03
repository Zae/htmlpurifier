<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\Config;
use HTMLPurifier\CSSDefinition;

/**
 * Class DefinitionTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class DefinitionTest extends TestCase
{
    /**
     * @test
     */
    public function test_setup(): void
    {
        $config = Config::createDefault();

        $def = \Mockery::spy(CSSDefinition::class . '[doSetup]')
                       ->makePartial()
                       ->shouldAllowMockingProtectedMethods();

        $def->shouldReceive('doSetup')
            ->once()

            ->withArgs([$config]);

        $def->setup($config);
    }

    /**
     * @test
     */
    public function test_setup_redundant(): void
    {
        $config = Config::createDefault();

        $def = \Mockery::spy(CSSDefinition::class . '[doSetup]')
                       ->makePartial()
                       ->shouldAllowMockingProtectedMethods();

        $def->shouldReceive('doSetup')
            ->never();

        $def->setup = true;
        $def->setup($config);
    }
}
