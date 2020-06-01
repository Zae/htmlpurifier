<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\ChildDef;

use HTMLPurifier\ChildDef\Optional;

/**
 * Class OptionalTest
 *
 * @package HTMLPurifier\Tests\Unit\ChildDef
 */
class OptionalTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new Optional('b | i');
    }

    /**
     * @test
     */
    public function testBasicUsage(): void
    {
        $this->assertResult('<b>Bold text</b><img />', '<b>Bold text</b>');
    }

    /**
     * @test
     */
    public function testRemoveForbiddenText(): void
    {
        $this->assertResult('Not allowed text', '');
    }

    /**
     * @test
     */
    public function testEmpty(): void
    {
        $this->assertResult('');
    }

    /**
     * @test
     */
    public function testWhitespace(): void
    {
        $this->assertResult(' ');
    }

    /**
     * @test
     */
    public function testMultipleWhitespace(): void
    {
        $this->assertResult('    ');
    }
}
