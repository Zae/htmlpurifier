<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\AttrDef;
use HTMLPurifier\AttrDef\HTML\Nmtokens;
use HTMLPurifier_ChildDef_Optional;
use HTMLPurifier_Config;
use HTMLPurifier_ElementDef;
use HTMLPurifier_HTMLModule;
use HTMLPurifier_HTMLModuleManager;
use Mockery;

/**
 * Class HTMLModuleManagerTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class HTMLModuleManagerTest extends TestCase
{
    private function createManager(): HTMLPurifier_HTMLModuleManager
    {
        $manager = new HTMLPurifier_HTMLModuleManager();

        $this->config->set('HTML.CustomDoctype', 'Blank');
        $manager->doctypes->register('Blank');

        $attrdef_nmtokens = new Nmtokens();

        $attrdef = Mockery::mock(AttrDef::class);
        $attrdef->expects()
            ->make('')
            ->andReturn($attrdef_nmtokens);

        $manager->attrTypes->set('NMTOKENS', $attrdef);
        return $manager;
    }

    /**
     * @test
     * @throws \HTMLPurifier_Exception
     */
    public function test_addModule(): void
    {
        $manager = $this->createManager();

        // ...but we add user modules

        $common_module = new HTMLPurifier_HTMLModule();
        $common_module->name = 'Common';
        $common_module->attr_collections['Common'] = ['class' => 'NMTOKENS'];
        $common_module->content_sets['Flow'] = 'Block | Inline';
        $manager->addModule($common_module);

        $structural_module = new HTMLPurifier_HTMLModule();
        $structural_module->name = 'Structural';
        $structural_module->addElement('p', 'Block', 'Inline', 'Common');
        $manager->addModule($structural_module);

        $formatting_module = new HTMLPurifier_HTMLModule();
        $formatting_module->name = 'Formatting';
        $formatting_module->addElement('em', 'Inline', 'Inline', 'Common');
        $manager->addModule($formatting_module);

        $unsafe_module = new HTMLPurifier_HTMLModule();
        $unsafe_module->name = 'Unsafe';
        $unsafe_module->safe = false;
        $unsafe_module->addElement('div', 'Block', 'Flow');
        $manager->addModule($unsafe_module);

        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Trusted', false);
        $config->set('HTML.CustomDoctype', 'Blank');

        $manager->setup($config);

        $attrdef_nmtokens = new Nmtokens();

        $p = new HTMLPurifier_ElementDef();
        $p->attr['class'] = $attrdef_nmtokens;
        $p->child = new HTMLPurifier_ChildDef_Optional(['em', '#PCDATA']);
        $p->content_model = 'em | #PCDATA';
        $p->content_model_type = 'optional';
        $p->descendants_are_inline = true;

        $em = new HTMLPurifier_ElementDef();
        $em->attr['class'] = $attrdef_nmtokens;
        $em->child = new HTMLPurifier_ChildDef_Optional(['em', '#PCDATA']);
        $em->content_model = 'em | #PCDATA';
        $em->content_model_type = 'optional';
        $em->descendants_are_inline = true;

        static::assertEquals(
            ['p' => $p, 'em' => $em],
            $manager->getElements()
        );

        // test trusted parameter override

        $div = new HTMLPurifier_ElementDef();
        $div->child = new HTMLPurifier_ChildDef_Optional(['p', 'div', 'em', '#PCDATA']);
        $div->content_model = 'p | div | em | #PCDATA';
        $div->content_model_type = 'optional';
        $div->descendants_are_inline = false;

        static::assertEquals($div, $manager->getElement('div', true));
    }

    /**
     * @test
     * @throws \HTMLPurifier_Exception
     */
    public function testAllowedModules(): void
    {
        $manager = new HTMLPurifier_HTMLModuleManager();
        $manager->doctypes->register(
            'Fantasy Inventory 1.0', true,
            ['Weapons', 'Magic']
        );

        // register these modules so it doesn't blow up
        $weapons_module = new HTMLPurifier_HTMLModule();
        $weapons_module->name = 'Weapons';
        $manager->registerModule($weapons_module);

        $magic_module = new HTMLPurifier_HTMLModule();
        $magic_module->name = 'Magic';
        $manager->registerModule($magic_module);

        $config = HTMLPurifier_Config::create([
            'HTML.CustomDoctype' => 'Fantasy Inventory 1.0',
            'HTML.AllowedModules' => 'Weapons'
        ]);
        $manager->setup($config);

        static::assertTrue(isset($manager->modules['Weapons']));
        static::assertFalse(isset($manager->modules['Magic']));
    }
}
