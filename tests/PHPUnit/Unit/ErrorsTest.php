<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier_Config;
use HTMLPurifier_Context;
use HTMLPurifier_ErrorCollector;
use Mockery;

/**
 * Class ErrorsTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
abstract class ErrorsTest extends TestCase
{
    protected $config, $context;
    protected $collector, $generator, $callCount;

    public function setUp(): void
    {
        $this->config = HTMLPurifier_Config::create(['Core.CollectErrors' => true]);
        $this->context = new HTMLPurifier_Context();
        $this->collector = Mockery::mock(HTMLPurifier_ErrorCollector::class);

        //$this->collector->prepare($this->context);
        $this->context->register('ErrorCollector', $this->collector);
        $this->callCount = 0;
    }

    protected function expectNoErrorCollection(): void
    {
        $this->collector->expects()
            ->send()
            ->never();
    }

    protected function expectErrorCollection(...$args): void
    {
        $this->collector->expects()
            ->send(...$args)
            ->once();
    }

    protected function expectContext($key, $value)
    {
//        $this->collector->expectContext($key, $value);
    }
}
