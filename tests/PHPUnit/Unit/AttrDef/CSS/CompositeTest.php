<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef\CSS;

use HTMLPurifier\AttrDef;
use HTMLPurifier\Tests\Unit\AttrDef\TestCase;
use HTMLPurifier\Config;
use HTMLPurifier\Context;
use Mockery;

/**
 * Class CompositeTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\CSS
 */
class CompositeTest extends TestCase
{
    protected $def1;
    protected $def2;

    /**
     * @test
     */
    public function testIt(): void
    {
        $config = Config::createDefault();
        $context = new Context();

        // first test: value properly validates on first definition
        // so second def is never called

        $def1 = Mockery::mock(AttrDef::class);
        $def2 = Mockery::mock(AttrDef::class);
        $defs = [&$def1, &$def2];
        $def = new Composite_Testable($defs);

        $input = 'FOOBAR';
        $output = 'foobar';
        $def1_params = [$input, $config, $context];

        $def1->expects()
            ->validate(...$def1_params)
            ->once()
            ->andReturn($output);

        $def2->expects()
            ->validate(Mockery::any())
            ->never();

        $result = $def->validate($input, $config, $context);
        static::assertEquals($output, $result);

        // second test, first def fails, second def works

        $def1 = Mockery::mock(AttrDef::class);
        $def2 = Mockery::mock(AttrDef::class);
        $defs = [&$def1, &$def2];
        $def = new Composite_Testable($defs);

        $input = 'BOOMA';
        $output = 'booma';
        $def_params = [$input, $config, $context];

        $def1->expects()
            ->validate(...$def_params)
            ->once()
            ->andReturn(false);

        $def2->expects()
            ->validate(...$def_params)
            ->once()
            ->andReturn($output);

        $result = $def->validate($input, $config, $context);
        static::assertEquals($output, $result);

        // third test, all fail, so composite fails

        $def1 = Mockery::mock(AttrDef::class);
        $def2 = Mockery::mock(AttrDef::class);
        $defs = [&$def1, &$def2];
        $def = new Composite_Testable($defs);

        $input = 'BOOMA';
        $output = false;
        $def_params = [$input, $config, $context];

        $def1->expects()
            ->validate(...$def_params)
            ->once()
            ->andReturn(false);

        $def2->expects()
            ->validate(...$def_params)
            ->once()
            ->andReturn(false);

        $result = $def->validate($input, $config, $context);
        static::assertEquals($output, $result);
    }
}

/**
 * Class Composite_Testable
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\CSS
 */
class Composite_Testable extends AttrDef\CSS\Composite
{
    /**
     * we need to pass by ref to get the mocks in
     *
     * @param AttrDef[] $defs List of HTMLPurifier_AttrDef objects
     */
    public function __construct(&$defs)
    {
        $this->defs =& $defs;
    }
}
