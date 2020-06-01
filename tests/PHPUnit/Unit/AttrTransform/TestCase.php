<?php
declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrTransform;

use HTMLPurifier\Tests\Unit\ComplexTestCase;

/**
 * Class TestCase
 *
 * @package HTMLPurifier\Tests\Unit\AttrTransform
 */
abstract class TestCase extends ComplexTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->func = 'transform';
    }
}
