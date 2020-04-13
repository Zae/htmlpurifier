<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\AttrDef;
use HTMLPurifier\ElementDef;
use HTMLPurifier_HTMLModule;
use Mockery;

/**
 * Class HTMLModuleTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class HTMLModuleTest extends TestCase
{
    /**
     * @test
     */
    public function test_addElementToContentSet(): void
    {
        $module = new HTMLPurifier_HTMLModule();

        $module->addElementToContentSet('b', 'Inline');
        static::assertEquals(['Inline' => 'b'], $module->content_sets);

        $module->addElementToContentSet('i', 'Inline');
        static::assertEquals(['Inline' => 'b | i'], $module->content_sets);
    }

    /**
     * @test
     */
    public function test_addElement(): void
    {
        $module = new HTMLPurifier_HTMLModule();
        $def = $module->addElement(
            'a', 'Inline', 'Optional: #PCDATA', ['Common'],
            [
                'href' => 'URI'
            ]
        );

        $module2 = new HTMLPurifier_HTMLModule();
        $def2 = new ElementDef();
        $def2->content_model = '#PCDATA';
        $def2->content_model_type = 'optional';
        $def2->attr = [
            'href' => 'URI',
            0 => ['Common']
        ];
        $module2->info['a'] = $def2;
        $module2->elements = ['a'];
        $module2->content_sets['Inline'] = 'a';

        static::assertEquals($module, $module2);
        static::assertEquals($def, $def2);
        static::assertEquals($def, $module->info['a']);

    }

    /**
     * @test
     */
    public function test_parseContents(): void
    {
        $module = new HTMLPurifier_HTMLModule();

        // pre-defined templates
        static::assertEquals(
            ['optional', 'Inline | #PCDATA'],
            $module->parseContents('Inline')
        );

        static::assertEquals(
            ['optional', 'Flow | #PCDATA'],
            $module->parseContents('Flow')
        );

        static::assertEquals(
            ['empty', ''],
            $module->parseContents('Empty')
        );

        // normalization procedures
        static::assertEquals(
            ['optional', 'a'],
            $module->parseContents('optional: a')
        );

        static::assertEquals(
            ['optional', 'a'],
            $module->parseContents('OPTIONAL :a')
        );

        static::assertEquals(
            ['optional', 'a'],
            $module->parseContents('Optional: a')
        );

        // others
        static::assertEquals(
            ['optional', 'a | b | c'],
            $module->parseContents('Optional: a | b | c')
        );

        // object pass-through
        $mock = Mockery::mock(AttrDef::class);
        static::assertEquals(
            [null, null],
            $module->parseContents($mock)
        );
    }

    /**
     * @test
     */
    public function test_mergeInAttrIncludes(): void
    {
        $module = new HTMLPurifier_HTMLModule();

        $attr = [];
        $module->mergeInAttrIncludes($attr, 'Common');
        static::assertEquals([0 => ['Common']], $attr);

        $attr = ['a' => 'b'];
        $module->mergeInAttrIncludes($attr, ['Common', 'Good']);
        static::assertEquals(['a' => 'b', 0 => ['Common', 'Good']], $attr);
    }

    /**
     * @test
     */
    public function test_addBlankElement(): void
    {
        $module = new HTMLPurifier_HTMLModule();
        $def = $module->addBlankElement('a');

        $def2 = new ElementDef();
        $def2->standalone = false;

        static::assertEquals($module->info['a'], $def);
        static::assertEquals($def, $def2);

    }

    /**
     * @test
     */
    public function test_makeLookup(): void
    {
        $module = new HTMLPurifier_HTMLModule();

        static::assertEquals(
            ['foo' => true],
            $module->makeLookup('foo')
        );

        static::assertEquals(
            ['foo' => true],
            $module->makeLookup(['foo'])
        );

        static::assertEquals(
            ['foo' => true, 'two' => true],
            $module->makeLookup('foo', 'two')
        );

        static::assertEquals(
            ['foo' => true, 'two' => true],
            $module->makeLookup(['foo', 'two'])
        );
    }
}
