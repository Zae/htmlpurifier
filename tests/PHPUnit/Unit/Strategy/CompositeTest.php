<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Strategy;

use HTMLPurifier\Tests\Unit\TestCase;
use HTMLPurifier\Config;
use HTMLPurifier\Context;
use HTMLPurifier\Strategy;
use HTMLPurifier\Strategy\Composite;
use Mockery;

// doesn't use Strategy TestCase
class CompositeTest extends TestCase
{
    /**
     * @test
     */
    public function test(): void
    {
        // setup a bunch of mock strategies to inject into our composite test

        $mock_1 = Mockery::mock(Strategy::class);
        $mock_2 = Mockery::mock(Strategy::class);
        $mock_3 = Mockery::mock(Strategy::class);

        // setup the object

        $strategies = [&$mock_1, &$mock_2, &$mock_3];
        $composite = new HTMLPurifier_Strategy_Composite_Test($strategies);

        // setup expectations

        $input_1 = 'This is raw data';
        $input_2 = 'Processed by 1';
        $input_3 = 'Processed by 1 and 2';
        $input_4 = 'Processed by 1, 2 and 3'; // expected output

        $config = Mockery::mock(Config::class);
        $context = Mockery::mock(Context::class);

        $params_1 = [$input_1, $config, $context];
        $params_2 = [$input_2, $config, $context];
        $params_3 = [$input_3, $config, $context];

        $mock_1->expects()
            ->execute(...$params_1)
            ->once()
            ->andReturn($input_2);

        $mock_2->expects()
            ->execute(...$params_2)
            ->once()
            ->andReturn($input_3);

        $mock_3->expects()
            ->execute(...$params_3)
            ->once()
            ->andReturn($input_4);

        // perform test

        $output = $composite->execute($input_1, $config, $context);
        static::assertEquals($input_4, $output);
    }
}

/**
 * Special Testing version of the HTMLPurifier\Strategy\HTMLPurifier_Strategy_Composite class that uses referenced
 * strategies for mocking purposes.
 *
 * Class HTMLPurifier_Strategy_Composite_Test
 *
 * @package HTMLPurifier\Tests\Unit\Strategy
 */
class HTMLPurifier_Strategy_Composite_Test extends Composite
{
    /**
     * HTMLPurifier_Strategy_Composite_Test constructor.
     *
     * @param $strategies
     */
    public function __construct(&$strategies)
    {
        $this->strategies =& $strategies;
    }
}
