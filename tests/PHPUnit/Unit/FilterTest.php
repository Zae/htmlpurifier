<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\Config;
use HTMLPurifier\Context;
use HTMLPurifier\Filter;

/**
 * Class FilterTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class FilterTest extends TestCase
{
    /**
     * @test
     */
    public function test(): void
    {
        $config = Config::createDefault();
        $context = new Context();

        $filter = new Filter();

        static::assertEquals('a', $filter->preFilter('a', $config, $context));
        static::assertEquals('a', $filter->postFilter('a', $config, $context));
    }
}
