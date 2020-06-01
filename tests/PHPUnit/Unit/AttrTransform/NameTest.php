<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrTransform;

use HTMLPurifier\AttrTransform\Name;

/**
 * Class NameTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrTransform
 */
class NameTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new Name();
    }

    /**
     * @test
     */
    public function testEmpty(): void
    {
        $this->assertResult([]);
    }

    /**
     * @test
     */
    public function testTransformNameToID(): void
    {
        $this->assertResult(
            ['name' => 'free'],
            ['id' => 'free']
        );
    }

    /**
     * @test
     */
    public function testExistingIDOverridesName(): void
    {
        $this->assertResult(
            ['name' => 'tryit', 'id' => 'tobad'],
            ['id' => 'tobad']
        );
    }
}
