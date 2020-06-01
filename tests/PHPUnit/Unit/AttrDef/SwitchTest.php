<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef;

use HTMLPurifier\AttrDef;
use HTMLPurifier\AttrDef\Switcher;
use HTMLPurifier\Token\Start;
use Mockery;

/**
 * Class SwitchTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\CSS
 */
class SwitchTest extends TestCase
{
    protected $with, $without;

    protected function setUp(): void
    {
        parent::setUp();

        $this->with = Mockery::mock(AttrDef::class);
        $this->without = Mockery::mock(AttrDef::class);
        $this->def = new Switcher('tag', $this->with, $this->without);
    }

    /**
     * @test
     */
    public function testWith(): void
    {
        $token = new Start('tag');
        $this->context->register('CurrentToken', $token);

        $this->with->expects()
            ->validate('bar', $this->config, $this->context)
            ->once()
            ->andReturn('foo');

        $this->assertDef('bar', 'foo');
    }

    /**
     * @test
     */
    public function testWithout(): void
    {
        $token = new Start('other-tag');
        $this->context->register('CurrentToken', $token);

        $this->without->expects()
            ->validate('bar', $this->config, $this->context)
            ->once()
            ->andReturn('foo');

        $this->assertDef('bar', 'foo');
    }
}
