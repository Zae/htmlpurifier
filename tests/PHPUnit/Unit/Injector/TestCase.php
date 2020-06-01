<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Injector;

use HTMLPurifier\Strategy\MakeWellFormed;

/**
 * Class TestCase
 *
 * @package HTMLPurifier\Tests\Unit\Injector
 */
abstract class TestCase extends \HTMLPurifier\Tests\Unit\Strategy\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new MakeWellFormed();
    }
}
