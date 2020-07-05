<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\ConfigSchema;

use HTMLPurifier\Tests\Unit\TestCase;
use HTMLPurifier\ConfigSchema\Interchange;
use HTMLPurifier\ConfigSchema\Interchange\Directive;
use HTMLPurifier\ConfigSchema\Interchange\Id;
use HTMLPurifier\ConfigSchema\Validator;

/**
 * Class ValidatorTest
 *
 * @package HTMLPurifier\Tests\Unit\ConfigSchema
 */
class ValidatorTest extends TestCase
{
    private $validator;
    private $interchange;

    protected function setUp(): void
    {
        $this->validator = new Validator();
        $this->interchange = new Interchange();
    }

    /**
     * @test
     */
    public function testDirectiveIntegrityViolation(): void
    {
        $d = $this->makeDirective('Ns.Dir');
        $d->id = new Id('Ns.Dir2');
        $this->expectValidationException("Integrity violation: key 'Ns.Dir' does not match internal id 'Ns.Dir2'");
        $this->validator->validate($this->interchange);
    }

    /**
     * @test
     */
    public function testDirectiveTypeNotEmpty(): void
    {
        $d = $this->makeDirective('Ns.Dir');
        $d->default = 0;
        $d->description = 'Description';

        $this->expectValidationException("Type in directive 'Ns.Dir' must not be empty");
        $this->validator->validate($this->interchange);
    }

    /**
     * @test
     */
    public function testDirectiveDefaultInvalid(): void
    {
        $d = $this->makeDirective('Ns.Dir');
        $d->default = 'asdf';
        $d->type = 'int';
        $d->description = 'Description';

        $this->expectValidationException("Default in directive 'Ns.Dir' had error: Expected type int, got string");
        $this->validator->validate($this->interchange);
    }

    /**
     * @test
     */
    public function testDirectiveTypeAllowsNullIsBool(): void
    {
        $d = $this->makeDirective('Ns.Dir');
        $d->default = 0;
        $d->type = 'int';
        $d->description = 'Description';
        $d->typeAllowsNull = 'yes';

        $this->expectValidationException("TypeAllowsNull in directive 'Ns.Dir' must be a boolean");
        $this->validator->validate($this->interchange);
    }

    /**
     * @test
     */
    public function testDirectiveValueAliasesIsArray(): void
    {
        $d = $this->makeDirective('Ns.Dir');
        $d->default = 'a';
        $d->type = 'string';
        $d->description = 'Description';
        $d->valueAliases = 2;

        $this->expectValidationException("ValueAliases in directive 'Ns.Dir' must be an array");
        $this->validator->validate($this->interchange);
    }

    /**
     * @test
     */
    public function testDirectiveAllowedIsLookup(): void
    {
        $d = $this->makeDirective('Ns.Dir');
        $d->default = 'foo';
        $d->type = 'string';
        $d->description = 'Description';
        $d->allowed = ['foo' => 1];

        $this->expectValidationException("Allowed in directive 'Ns.Dir' must be a lookup array");
        $this->validator->validate($this->interchange);
    }

    // helper functions

    /**
     * @param string|int $key
     *
     * @return Directive
     * @throws \HTMLPurifier\ConfigSchema\Exception
     * @throws \HTMLPurifier_ConfigSchema_Exception
     */
    private function makeDirective($key): Directive
    {
        $directive = new Directive();
        $directive->id = new Id($key);
        $this->interchange->addDirective($directive);
        return $directive;
    }

    /**
     * @param string $msg
     */
    private function expectValidationException(string $msg): void
    {
        $this->expectExceptionMessage($msg);
        $this->expectException(\Exception::class);
    }
}
