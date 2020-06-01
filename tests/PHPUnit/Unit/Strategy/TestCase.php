<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Strategy;

use HTMLPurifier\Tests\Unit\ComplexTestCase;

/**
 * Class TestCase
 *
 * @package HTMLPurifier\Tests\Unit\Strategy
 */
abstract class TestCase extends ComplexTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->func      = 'execute';
        $this->to_tokens = true;
        $this->to_html   = true;
    }
}
