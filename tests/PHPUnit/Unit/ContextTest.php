<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\Context;
use HTMLPurifier\Exception;
use HTMLPurifier\IDAccumulator;
use Mockery;

/**
 * Class ContextTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class ContextTest extends TestCase
{
    protected $context;

    protected function setUp(): void
    {
        $this->context = new Context();
    }

    /**
     * @test
     */
    public function testStandardUsage(): void
    {
        static::assertFalse($this->context->exists('IDAccumulator'));

        $accumulator = Mockery::mock(IDAccumulator::class);
        $this->context->register('IDAccumulator', $accumulator);
        static::assertTrue($this->context->exists('IDAccumulator'));

        $accumulator_2 =& $this->context->get('IDAccumulator');
        static::assertSame($accumulator, $accumulator_2);

        $this->context->destroy('IDAccumulator');
        static::assertFalse($this->context->exists('IDAccumulator'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Attempted to retrieve non-existent variable IDAccumulator');

        $accumulator_3 =& $this->context->get('IDAccumulator');
        static::assertNull($accumulator_3);

        $this->expectError();
        $this->expectErrorMessage('Attempted to destroy non-existent variable IDAccumulator');
        $this->context->destroy('IDAccumulator');
    }

    /**
     * @test
     */
    public function testReRegister(): void
    {
        $var = true;
        $this->context->register('OnceOnly', $var);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Name OnceOnly produces collision, cannot re-register');

        $this->context->register('OnceOnly', $var);

        // destroy it, now registration is okay
        $this->context->destroy('OnceOnly');
        $this->context->register('OnceOnly', $var);
    }

    /**
     * @test
     */
    public function test_loadArray(): void
    {
        // references can be *really* wonky!

        $context_manual = new Context();
        $context_load   = new Context();

        $var1 = 1;
        $var2 = 2;

        $context_manual->register('var1', $var1);
        $context_manual->register('var2', $var2);

        // you MUST set up the references when constructing the array,
        // otherwise the registered version will be a copy
        $array = [
            'var1' => &$var1,
            'var2' => &$var2
        ];

        $context_load->loadArray($array);
        static::assertEquals($context_manual, $context_load);

        $var1 = 10;
        $var2 = 20;

        static::assertEquals($context_manual, $context_load);
    }

    /**
     * @test
     */
    public function testNull(): void
    {
        $context = new Context();
        $var = NULL;
        $context->register('var', $var);

        static::assertNull($context->get('var'));
        $context->destroy('var');
    }
}
