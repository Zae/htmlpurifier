<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Definition;

use HTMLPurifier\AttrDef\CSS\AlphaValue;
use HTMLPurifier\AttrDef\CSS\Color;
use HTMLPurifier\AttrDef\CSS\Composite;
use HTMLPurifier\AttrDef\CSS\ImportantDecorator;
use HTMLPurifier\AttrDef\Enum;
use HTMLPurifier\AttrDef\Integer;
use HTMLPurifier\CSSDefinition;
use HTMLPurifier\Tests\Unit\TestCase;

/**
 * Class CssDefinitionTest
 *
 * @package HTMLPurifier\Tests\Unit\Definition
 */
class CssDefinitionTest extends TestCase
{
    /**
     * @test
     */
    public function test(): void
    {
        $this->config->set('CSS.Proprietary', true);
        $this->config->set('CSS.AllowTricky', true);
        $this->config->set('CSS.Trusted', true);

        $def = new CSSDefinition();
        $def->setup($this->config);

        $allow_important = $this->config->get('CSS.AllowImportant');

        $textAlignDecorator = new ImportantDecorator(
            new Enum(
                ['left', 'right', 'center', 'justify'],
                false
            ),
            $allow_important
        );

        $borderStyleDecorator = new ImportantDecorator(
            new Enum(
                [
                    'none',
                    'hidden',
                    'dotted',
                    'dashed',
                    'solid',
                    'double',
                    'groove',
                    'ridge',
                    'inset',
                    'outset'
                ],
                false
            ),
            $allow_important
        );

        static::assertEquals($textAlignDecorator, $def->info['text-align']);
        static::assertEquals($borderStyleDecorator, $def->info['border-bottom-style']);
        static::assertEquals($borderStyleDecorator, $def->info['border-right-style']);
        static::assertEquals($borderStyleDecorator, $def->info['border-left-style']);
        static::assertEquals($borderStyleDecorator, $def->info['border-top-style']);

        // Proprietary
        $color = new ImportantDecorator(new Color(), $allow_important);
        static::assertEquals($color, $def->info['scrollbar-arrow-color']);

        $alpha = new ImportantDecorator(new AlphaValue(), $allow_important);
        static::assertEquals($alpha, $def->info['-moz-opacity']);

        // Tricky
        static::assertEquals($alpha, $def->info['opacity']);

        // Trusted
        $composite = new ImportantDecorator(
            new Composite(
                [
                    new Integer(),
                    new Enum(['auto']),
                ]
            ),
            $allow_important
        );
        static::assertEquals($composite, $def->info['z-index']);
    }
}
