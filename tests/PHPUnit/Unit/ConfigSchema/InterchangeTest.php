<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\ConfigSchema;

use HTMLPurifier\Tests\Unit\TestCase;
use HTMLPurifier_ConfigSchema_Interchange;
use HTMLPurifier_ConfigSchema_Interchange_Directive;
use HTMLPurifier_ConfigSchema_Interchange_Id;

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
        $this->interchange = new HTMLPurifier_ConfigSchema_Interchange();
    }

    public function testAddDirective()
    {
        $v = new HTMLPurifier_ConfigSchema_Interchange_Directive();
        $v->id = new HTMLPurifier_ConfigSchema_Interchange_Id('Namespace.Directive');

        $this->interchange->addDirective($v);

        static::assertEquals($v, $this->interchange->directives['Namespace.Directive']);
    }
}
