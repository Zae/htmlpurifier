<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\HTMLModule;

use HTMLPurifier\Exception;
use HTMLPurifier\HTMLModule\Tidy;
use HTMLPurifier\Tests\Unit\TestCase;
use \HTMLPurifier\Config;
use Mockery;

/**
 * Class TidyTest
 *
 * @package HTMLPurifier\Tests\Unit\HTMLModule
 */
class TidyTest extends TestCase
{
    /**
     * @test
     */
    public function test_getFixesForLevel(): void
    {
        $module = new Tidy();
        $module->fixesForLevel['light'][]  = 'light-fix';
        $module->fixesForLevel['medium'][] = 'medium-fix';
        $module->fixesForLevel['heavy'][]  = 'heavy-fix';

        static::assertEquals(
            [],
            $module->getFixesForLevel('none')
        );
        static::assertEquals(
            ['light-fix' => true],
            $module->getFixesForLevel('light')
        );
        static::assertEquals(
            ['light-fix' => true, 'medium-fix' => true],
            $module->getFixesForLevel('medium')
        );
        static::assertEquals(
            ['light-fix' => true, 'medium-fix' => true, 'heavy-fix' => true],
            $module->getFixesForLevel('heavy')
        );

        $this->expectError();
        $this->expectErrorMessage('Tidy level turbo not recognized');
        $module->getFixesForLevel('turbo');
    }

    /**
     * @test
     */
    public function test_setup(): void
    {
        // initialize partial mock
        $module = Mockery::mock(Tidy::class)->makePartial();

        $module->fixesForLevel['light']  = ['light-fix-1', 'light-fix-2'];
        $module->fixesForLevel['medium'] = ['medium-fix-1', 'medium-fix-2'];
        $module->fixesForLevel['heavy']  = ['heavy-fix-1', 'heavy-fix-2'];

        $j = 0;
        $fixes = [
            'light-fix-1'  => $lf1 = $j++,
            'light-fix-2'  => $lf2 = $j++,
            'medium-fix-1' => $mf1 = $j++,
            'medium-fix-2' => $mf2 = $j++,
            'heavy-fix-1'  => $hf1 = $j++,
            'heavy-fix-2'  => $hf2 = $j++
        ];

        $module->expects()
            ->makeFixes()
            ->times(5)
            ->andReturn($fixes);

        $config = \HTMLPurifier\Config::create([
            'HTML.TidyLevel' => 'none'
        ]);

        $module->expects()
            ->populate([])
            ->times(1);

        $module->setup($config);

        // basic levels

        $config = \HTMLPurifier\Config::create([
            'HTML.TidyLevel' => 'light'
        ]);

        $module->expects()
            ->populate([
                'light-fix-1' => $lf1,
                'light-fix-2' => $lf2
            ])
            ->times(1);

        $module->setup($config);

        $config = \HTMLPurifier\Config::create([
            'HTML.TidyLevel' => 'heavy'
        ]);

        $module->expects()
            ->populate([
                'light-fix-1'  => $lf1,
                'light-fix-2'  => $lf2,
                'medium-fix-1' => $mf1,
                'medium-fix-2' => $mf2,
                'heavy-fix-1'  => $hf1,
                'heavy-fix-2'  => $hf2
            ])
            ->times(1);

        $module->setup($config);

        // fine grained tuning

        $config = \HTMLPurifier\Config::create([
            'HTML.TidyLevel' => 'none',
            'HTML.TidyAdd'   => ['light-fix-1', 'medium-fix-1']
        ]);

        $module->expects()
            ->populate([
                'light-fix-1' => $lf1,
                'medium-fix-1' => $mf1
            ])
            ->times(1);

        $module->setup($config);

        $config = \HTMLPurifier\Config::create([
            'HTML.TidyLevel' => 'medium',
            'HTML.TidyRemove'   => ['light-fix-1', 'medium-fix-1']
        ]);

        $module->expects()
            ->populate([
                'light-fix-2' => $lf2,
                'medium-fix-2' => $mf2
            ])
            ->times(1);

        $module->setup($config);
    }

    /**
     * @test
     */
    public function test_makeFixesForLevel(): void
    {
        $module = new Tidy();
        $module->defaultLevel = 'heavy';

        $module->makeFixesForLevel([
            'fix-1' => 0,
            'fix-2' => 1,
            'fix-3' => 2
        ]);

        static::assertEquals(['fix-1', 'fix-2', 'fix-3'], $module->fixesForLevel['heavy']);
        static::assertEquals([], $module->fixesForLevel['medium']);
        static::assertEquals([], $module->fixesForLevel['light']);
    }

    /**
     * @test
     */
    public function test_makeFixesForLevel_undefinedLevel(): void
    {
        $module = new Tidy();
        $module->defaultLevel = 'bananas';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Default level bananas does not exist');

        $module->makeFixesForLevel([
            'fix-1' => 0
        ]);
    }

    /**
     * @test
     */
    public function test_getFixType(): void
    {
        // syntax needs documenting

        $module = new Tidy();

        static::assertEquals(
            ['tag_transform', ['element' => 'a']],
            $module->getFixType('a')
        );

        static::assertEquals(
            $reuse = ['attr_transform_pre', ['element' => 'a', 'attr' => 'href']],
            $module->getFixType('a@href')
        );

        static::assertEquals(
            $reuse,
            $module->getFixType('a@href#pre')
        );

        static::assertEquals(
            ['attr_transform_post', ['element' => 'a', 'attr' => 'href']],
            $module->getFixType('a@href#post')
        );

        static::assertEquals(
            ['attr_transform_pre', ['element' => 'xml:foo', 'attr' => 'xml:bar']],
            $module->getFixType('xml:foo@xml:bar')
        );

        static::assertEquals(
            ['child', ['element' => 'blockquote']],
            $module->getFixType('blockquote#child')
        );

        static::assertEquals(
            ['attr_transform_pre', ['attr' => 'lang']],
            $module->getFixType('@lang')
        );

        static::assertEquals(
            ['attr_transform_post', ['attr' => 'lang']],
            $module->getFixType('@lang#post')
        );
    }

    /**
     * @test
     */
    public function test_populate(): void
    {
        $i = 0;

        $module = new Tidy();
        $module->populate([
            'element' => $element = $i++,
            'element@attr' => $attr = $i++,
            'element@attr#post' => $attr_post = $i++,
            'element#child' => $child = $i++,
            'element#content_model_type' => $content_model_type = $i++,
            '@attr' => $global_attr = $i++,
            '@attr#post' => $global_attr_post = $i++
        ]);

        $module2 = new Tidy();
        $e = $module2->addBlankElement('element');
        $e->attr_transform_pre['attr'] = $attr;
        $e->attr_transform_post['attr'] = $attr_post;
        $e->child = $child;
        $e->content_model_type = $content_model_type;
        $module2->info_tag_transform['element'] = $element;
        $module2->info_attr_transform_pre['attr'] = $global_attr;
        $module2->info_attr_transform_post['attr'] = $global_attr_post;

        static::assertEquals($module, $module2);
    }
}
