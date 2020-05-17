<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\ChildDef;

use HTMLPurifier\Tests\Unit\ComplexTestCase;

/**
 * Class TestCase
 *
 * @package HTMLPurifier\Tests\Unit\ChildDef
 */
abstract class TestCase extends ComplexTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->obj       = null;
        $this->func      = 'validateChildren';
        $this->to_html   = true;
        $this->to_node_list = true;
    }
}
