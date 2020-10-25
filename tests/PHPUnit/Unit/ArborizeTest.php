<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\Arborize;
use HTMLPurifier\Node\Element;

/**
 * Class ArborizeTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class ArborizeTest extends TestCase
{
    /**
     * @test
     */
    public function test(): void
    {
        $node = new Element('div');
        $node->children[] = new Element('div');
        $node->children[] = new Element('div');
        $node->children[] = new Element('div');

        $flat = Arborize::flatten($node);

        static::assertCount(6, $flat);
    }
}
