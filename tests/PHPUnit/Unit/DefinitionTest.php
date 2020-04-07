<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier_Config;

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
        static::markTestSkipped('what\'s a HTMLPurifier_DefinitionTestable... who knows?');
        $def = new HTMLPurifier_DefinitionTestable();

        $config = HTMLPurifier_Config::createDefault();
        $def->expectOnce('doSetup', [$config]);
        $def->setup($config);
    }

    /**
     * @test
     */
    public function test_setup_redundant(): void
    {
        static::markTestSkipped('what\'s a HTMLPurifier_DefinitionTestable... who knows?');

        $def = new HTMLPurifier_DefinitionTestable();

        $config = HTMLPurifier_Config::createDefault();
        $def->expectNever('doSetup');
        $def->setup = true;
        $def->setup($config);
    }
}
