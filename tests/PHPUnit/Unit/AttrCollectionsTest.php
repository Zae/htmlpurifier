<?php
declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\AttrCollections;
use HTMLPurifier\AttrDef\HTML\Color;
use HTMLPurifier\AttrDef\URI;
use HTMLPurifier_AttrTypes;
use HTMLPurifier_HTMLModule;
use Mockery;

/**
 * Class AttrCollectionsTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class AttrCollectionsTest extends TestCase
{
    /**
     * @test
     */
    public function testConstruction(): void
    {
        $collections = Mockery::mock(AttrCollections::class)->makePartial();
        $collections->expects()
            ->performInclusions(Mockery::any())
            ->times(3)
            ->andReturn(true);

        $collections->expects()
            ->expandIdentifiers(Mockery::any(), Mockery::any())
            ->times(3)
            ->andReturn(true);

        $types = Mockery::mock(HTMLPurifier_AttrTypes::class);

        $modules = [];

        $modules['Module1'] = new HTMLPurifier_HTMLModule();
        $modules['Module1']->attr_collections = [
            'Core' => [
                0 => ['Soup', 'Undefined'],
                'attribute' => 'Type',
                'attribute-2' => 'Type2',
            ],
            'Soup' => [
                'attribute-3' => 'Type3-old' // overwritten
            ]
        ];

        $modules['Module2'] = new HTMLPurifier_HTMLModule();
        $modules['Module2']->attr_collections = [
            'Core' => [
                0 => ['Brocolli']
            ],
            'Soup' => [
                'attribute-3' => 'Type3'
            ],
            'Brocolli' => []
        ];

        $collections->doConstruct($types, $modules);
        // this is without identifier expansion or inclusions
        static::assertEquals(
            [
                'Core' => [
                    0 => ['Soup', 'Undefined', 'Brocolli'],
                    'attribute' => 'Type',
                    'attribute-2' => 'Type2'
                ],
                'Soup' => [
                    'attribute-3' => 'Type3'
                ],
                'Brocolli' => []
            ],
            $collections->info
        );
    }

    /**
     * @test
     */
    public function testPerformInclusions(): void
    {
        $types = Mockery::mock(HTMLPurifier_AttrTypes::class);

        $collections = new AttrCollections($types, []);
        $collections->info = [
            'Core' => [0 => ['Inclusion', 'Undefined'], 'attr-original' => 'Type'],
            'Inclusion' => [0 => ['SubInclusion'], 'attr' => 'Type'],
            'SubInclusion' => ['attr2' => 'Type']
        ];

        $collections->performInclusions($collections->info['Core']);
        static::assertEquals(
            $collections->info['Core'],
            [
                'attr-original' => 'Type',
                'attr' => 'Type',
                'attr2' => 'Type'
            ]
        );

        // test recursive
        $collections->info = [
            'One' => [0 => ['Two'], 'one' => 'Type'],
            'Two' => [0 => ['One'], 'two' => 'Type']
        ];
        $collections->performInclusions($collections->info['One']);
        static::assertEquals(
            $collections->info['One'],
            [
                'one' => 'Type',
                'two' => 'Type'
            ]
        );
    }

    /**
     * @test
     */
    public function testExpandIdentifiers(): void
    {
        $types = Mockery::mock(HTMLPurifier_AttrTypes::class);

        $collections = new AttrCollections($types, []);

        $attr = [
            'attr1' => 'Color',
            'attr2*' => 'URI'
        ];
        $c_object = new Color();
        $u_object = new URI();

        $types->expects()
            ->get('Color')
            ->andReturn($c_object);

        $types->expects()
            ->get('URI')
            ->andReturn($u_object);

        $collections->expandIdentifiers($attr, $types);

        $u_object->required = true;
        static::assertEquals(
            $attr,
            [
                'attr1' => $c_object,
                'attr2' => $u_object
            ]
        );
    }
}
