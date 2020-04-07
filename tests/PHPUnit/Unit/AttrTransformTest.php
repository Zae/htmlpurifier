<?php
declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\AttrDef;
use HTMLPurifier_AttrTransform;
use Mockery;

/**
 * Class AttrTransformTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class AttrTransformTest extends TestCase
{
    /**
     * @test
     */
    public function testParseCDATA(): void
    {
        $t = Mockery::mock(HTMLPurifier_AttrTransform::class)->makePartial();

//        $t->expects()
//            ->transform(Mockery::any(), Mockery::any(), Mockery::any())
//            ->andReturn([]);

        $attr = [];
        $t->prependCSS($attr, 'style:new;');
        static::assertEquals(['style' => 'style:new;'], $attr);

        $attr = ['style' => 'style:original;'];
        $t->prependCSS($attr, 'style:new;');
        static::assertEquals(['style' => 'style:new;style:original;'], $attr);

        $attr = ['style' => 'style:original;', 'misc' => 'un-related'];
        $t->prependCSS($attr, 'style:new;');
        static::assertEquals(['style' => 'style:new;style:original;', 'misc' => 'un-related'], $attr);
    }

    public function testConfiscateAttr(): void
    {
        $t = Mockery::mock(HTMLPurifier_AttrTransform::class)->makePartial();

//        $t->expects()
//            ->transform(Mockery::any(), Mockery::any(), Mockery::any())
//            ->andReturn([]);

        $attr = ['flavor' => 'sweet'];
        static::assertEquals('sweet', $t->confiscateAttr($attr, 'flavor'));
        static::assertEquals([], $attr);

        $attr = ['flavor' => 'sweet'];
        static::assertEquals(null, $t->confiscateAttr($attr, 'color'));
        static::assertEquals(['flavor' => 'sweet'], $attr);
    }
}
