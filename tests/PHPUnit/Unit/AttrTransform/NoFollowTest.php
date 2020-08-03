<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrTransform;

use HTMLPurifier\AttrTransform\Nofollow;

/**
 * Class NoFollowTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrTransform
 */
class NoFollowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new Nofollow();
    }

    /**
     * @test
     */
    public function test(): void
    {
        $attr = ['id' => ''];

        static::assertEquals($attr, $this->obj->transform($attr, $this->config, $this->context));
    }
}
