<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\ConfigSchema;

use HTMLPurifier\Tests\Unit\TestCase;
use HTMLPurifier\ConfigSchema\ValidatorAtom;
use stdClass;

/**
 * Class ValidatorAtomTest
 *
 * @package HTMLPurifier\Tests\Unit\ConfigSchema
 */
class ValidatorAtomTest extends TestCase
{
    /**
     * @param $msg
     */
    private function expectValidationException(string $msg): void
    {
        $this->expectExceptionMessage($msg);
        $this->expectException(\Exception::class);
    }

    /**
     * @param $value
     *
     * @return ValidatorAtom
     */
    private function makeAtom($value): ValidatorAtom
    {
        $obj = new stdClass();
        $obj->property = $value;

        // Note that 'property' and 'context' are magic wildcard values
        return new ValidatorAtom('context', $obj, 'property');
    }

    /**
     * @test
     */
    public function testAssertIsString(): void
    {
        $this->makeAtom('foo')->assertIsString();
        static::assertReached();
    }

    /**
     * @test
     */
    public function testAssertIsStringFail(): void
    {
        $this->expectValidationException("Property in context must be a string");
        $this->makeAtom(3)->assertIsString();
    }

    /**
     * @test
     */
    public function testAssertNotNull(): void
    {
        $this->makeAtom('foo')->assertNotNull();
        static::assertReached();
    }

    /**
     * @test
     */
    public function testAssertNotNullFail(): void
    {
        $this->expectValidationException("Property in context must not be null");
        $this->makeAtom(null)->assertNotNull();
    }

    /**
     * @test
     */
    public function testAssertAlnum(): void
    {
        $this->makeAtom('foo2')->assertAlnum();
        static::assertReached();
    }

    /**
     * @test
     */
    public function testAssertAlnumFail(): void
    {
        $this->expectValidationException("Property in context must be alphanumeric");
        $this->makeAtom('%a')->assertAlnum();
    }

    /**
     * @test
     */
    public function testAssertAlnumFailIsString(): void
    {
        $this->expectValidationException("Property in context must be a string");
        $this->makeAtom(3)->assertAlnum();
    }

    /**
     * @test
     */
    public function testAssertNotEmpty(): void
    {
        $this->makeAtom('foo')->assertNotEmpty();
        static::assertReached();
    }

    /**
     * @test
     */
    public function testAssertNotEmptyFail(): void
    {
        $this->expectValidationException("Property in context must not be empty");
        $this->makeAtom('')->assertNotEmpty();
    }

    /**
     * @test
     */
    public function testAssertIsBool(): void
    {
        $this->makeAtom(false)->assertIsBool();
        static::assertReached();
    }

    /**
     * @test
     */
    public function testAssertIsBoolFail(): void
    {
        $this->expectValidationException("Property in context must be a boolean");
        $this->makeAtom('0')->assertIsBool();
    }

    /**
     * @test
     */
    public function testAssertIsArray(): void
    {
        $this->makeAtom([])->assertIsArray();
        static::assertReached();
    }

    /**
     * @test
     */
    public function testAssertIsArrayFail(): void
    {
        $this->expectValidationException("Property in context must be an array");
        $this->makeAtom('asdf')->assertIsArray();
    }

    /**
     * @test
     */
    public function testAssertIsLookup(): void
    {
        $this->makeAtom(['foo' => true])->assertIsLookup();
        static::assertReached();
    }

    /**
     * @test
     */
    public function testAssertIsLookupFail(): void
    {
        $this->expectValidationException("Property in context must be a lookup array");
        $this->makeAtom(['foo' => 4])->assertIsLookup();
    }

    /**
     * @test
     */
    public function testAssertIsLookupFailIsArray(): void
    {
        $this->expectValidationException("Property in context must be an array");
        $this->makeAtom('asdf')->assertIsLookup();
    }
}
