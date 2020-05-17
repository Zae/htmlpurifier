<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\HTMLModule;

use HTMLPurifier\Strategy\Core;

/**
 * Class TestCase
 *
 * @package HTMLPurifier\Tests\Unit\HTMLModule
 */
abstract class TestCase extends \HTMLPurifier\Tests\Unit\Strategy\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->obj = new Core();
    }
}
