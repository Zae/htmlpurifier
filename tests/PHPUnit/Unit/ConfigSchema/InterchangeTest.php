<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\ConfigSchema;

use HTMLPurifier\Tests\Unit\TestCase;
use HTMLPurifier\ConfigSchema\Interchange;
use HTMLPurifier\ConfigSchema\Interchange\Directive;
use HTMLPurifier\ConfigSchema\Interchange\Id;

/**
 * Class InterchangeTest
 *
 * @package HTMLPurifier\Tests\Unit\ConfigSchema
 */
class InterchangeTest extends TestCase
{
    protected $interchange;

    protected function setUp(): void
    {
        $this->interchange = new Interchange();
    }

    public function testAddDirective()
    {
        $v = new Directive();
        $v->id = new Id('Namespace.Directive');

        $this->interchange->addDirective($v);

        static::assertEquals($v, $this->interchange->directives['Namespace.Directive']);
    }
}
