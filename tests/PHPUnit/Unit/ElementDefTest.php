<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\ElementDef;

/**
 * Class ElementDefTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class ElementDefTest extends TestCase
{
    /**
     * @test
     */
    public function test_mergeIn(): void
    {
        $def1 = new ElementDef();
        $def2 = new ElementDef();
        $def3 = new ElementDef();

        $old = 1;
        $new = 2;
        $overloaded_old = 3;
        $overloaded_new = 4;
        $removed = 5;

        $def1->standalone = true;
        $def1->attr = [
            0 => ['old-include'],
            'old-attr' => $old,
            'overloaded-attr' => $overloaded_old,
            'removed-attr' => $removed,
        ];
        /*
        $def1->attr_transform_pre =
        $def1->attr_transform_post = array(
            'old-transform' => $old,
            'overloaded-transform' => $overloaded_old,
            'removed-transform' => $removed,
        );
         */
        $def1->attr_transform_pre[] = $old;
        $def1->attr_transform_post[] = $old;
        $def1->child = $overloaded_old;
        $def1->content_model = 'old';
        $def1->content_model_type = $overloaded_old;
        $def1->descendants_are_inline = false;
        $def1->excludes = [
            'old' => true,
            'removed-old' => true
        ];

        $def2->standalone = false;
        $def2->attr = [
            0 => ['new-include'],
            'new-attr' => $new,
            'overloaded-attr' => $overloaded_new,
            'removed-attr' => false,
        ];
        /*
        $def2->attr_transform_pre =
        $def2->attr_transform_post = array(
            'new-transform' => $new,
            'overloaded-transform' => $overloaded_new,
            'removed-transform' => false,
        );
         */
        $def2->attr_transform_pre[] = $new;
        $def2->attr_transform_post[] = $new;
        $def2->child = $new;
        $def2->content_model = '#SUPER | new';
        $def2->content_model_type = $overloaded_new;
        $def2->descendants_are_inline = true;
        $def2->excludes = [
            'new' => true,
            'removed-old' => false
        ];

        $def1->mergeIn($def2);
        $def1->mergeIn($def3); // empty, has no effect

        static::assertEquals(true, $def1->standalone);
        static::assertEquals($def1->attr, [
            0 => ['old-include', 'new-include'],
            'old-attr' => $old,
            'overloaded-attr' => $overloaded_new,
            'new-attr' => $new,
        ]);
        static::assertEquals($def1->attr_transform_pre, $def1->attr_transform_post);
        static::assertEquals($def1->attr_transform_pre, [$old, $new]);
        /*
        $this->assertIdentical($def1->attr_transform_pre, array(
            'old-transform' => $old,
            'overloaded-transform' => $overloaded_new,
            'new-transform' => $new,
        ));
         */
        static::assertEquals($def1->child, $new);
        static::assertEquals('old | new', $def1->content_model);
        static::assertEquals($def1->content_model_type, $overloaded_new);
        static::assertEquals(true, $def1->descendants_are_inline);
        static::assertEquals([
            'old' => true,
            'new' => true
        ], $def1->excludes);
    }
}
