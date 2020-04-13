<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier;
use HTMLPurifier_Config;
use HTMLPurifier\HTMLDefinition;
use HTMLPurifier\Injector;
use HTMLPurifier_Injector_Linkify;

/**
 * Class HTMLDefinitionTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class HTMLDefinitionTest extends TestCase
{
    /**
     * @test
     */
    public function test_parseTinyMCEAllowedList(): void
    {
        $def = new HTMLDefinition();

        // note: this is case-sensitive, but its config schema
        // counterpart is not. This is generally a good thing for users,
        // but it's a slight internal inconsistency

        static::assertEquals(
            [[], []],
            $def->parseTinyMCEAllowedList('')
        );

        static::assertEquals(
            [['a' => true, 'b' => true, 'c' => true], []],
            $def->parseTinyMCEAllowedList('a,b,c')
        );

        static::assertEquals(
            [['a' => true], ['a.x' => true, 'a.y' => true, 'a.z' => true]],
            $def->parseTinyMCEAllowedList('a[x|y|z]')
        );

        static::assertEquals(
            [[], ['*.id' => true]],
            $def->parseTinyMCEAllowedList('*[id]')
        );

        static::assertEquals(
            [['a' => true], ['a.*' => true]],
            $def->parseTinyMCEAllowedList('a[*]')
        );

        static::assertEquals(
            [['span' => true, 'strong' => true, 'a' => true],
                ['span.style' => true, 'a.href' => true, 'a.title' => true]],
            $def->parseTinyMCEAllowedList('span[style],strong,a[href|title]')
        );

        $val = [['span' => true, 'strong' => true, 'a' => true],
            ['span.style' => true, 'a.href' => true, 'a.title' => true]];

        static::assertEquals(
            $val,
            // alternate form:
            $def->parseTinyMCEAllowedList(
                'span[style]
strong
a[href|title]
')
        );

        static::assertEquals(
            $val,
            $def->parseTinyMCEAllowedList(' span [ style ], strong' . "\n\t" . 'a[href | title]')
        );
    }

    /**
     * @test
     * @throws \HTMLPurifier_Exception
     */
    public function test_Allowed(): void
    {
        $config1 = HTMLPurifier_Config::create([
            'HTML.AllowedElements' => ['b', 'i', 'p', 'a'],
            'HTML.AllowedAttributes' => ['a@href', '*@id']
        ]);

        $config2 = HTMLPurifier_Config::create([
            'HTML.Allowed' => 'b,i,p,a[href],*[id]'
        ]);

        static::assertEquals($config1->getHTMLDefinition(), $config2->getHTMLDefinition());
    }

    /**
     * @test
     * @throws \HTMLPurifier_Exception
     */
    public function test_AllowedElements(): void
    {
        $this->config->set('HTML.AllowedElements', 'p');
        $this->assertPurification_AllowedElements_p();
    }

    /**
     * @test
     */
    public function test_AllowedElements_multiple(): void
    {
        $this->config->set('HTML.AllowedElements', 'p,div');
        $this->assertPurification('<div><p><b>Jelly</b></p></div>', '<div><p>Jelly</p></div>');
    }

    /**
     * @test
     */
    public function test_AllowedElements_invalidElement(): void
    {
        $this->config->set('HTML.AllowedElements', 'obviously_invalid,p');
        
        $this->expectError();
        $this->expectErrorMessage("Element 'obviously_invalid' is not supported");
        
        $this->assertPurification_AllowedElements_p();
    }

    /**
     * @test
     */
    public function test_AllowedElements_invalidElement_xssAttempt(): void
    {
        $this->config->set('HTML.AllowedElements', '<script>,p');

        $this->expectError();
        $this->expectErrorMessage("Element '&lt;script&gt;' is not supported");

        $this->assertPurification_AllowedElements_p();
    }

    /**
     * @test
     */
    public function test_AllowedElements_multipleInvalidElements(): void
    {
        $this->config->set('HTML.AllowedElements', 'dr-wiggles,dr-pepper,p');

        $this->expectError();
        $this->expectErrorMessage("Element 'dr-wiggles' is not supported");
//        $this->expectErrorMessage("Element 'dr-pepper' is not supported");

        $this->assertPurification_AllowedElements_p();
    }

    /**
     * @test
     */
    public function test_AllowedElements_multipleInvalidElements2(): void
    {
        $this->config->set('HTML.AllowedElements', 'dr-pepper,dr-wiggles,p');

        $this->expectError();
//        $this->expectErrorMessage("Element 'dr-wiggles' is not supported");
        $this->expectErrorMessage("Element 'dr-pepper' is not supported");

        $this->assertPurification_AllowedElements_p();
    }

    /**
     * @test
     */
    public function test_AllowedAttributes_global_preferredSyntax(): void
    {
        $this->config->set('HTML.AllowedElements', ['p', 'br']);
        $this->config->set('HTML.AllowedAttributes', 'style');
        $this->assertPurification_AllowedAttributes_global_style();
    }

    /**
     * @test
     */
    public function test_AllowedAttributes_global_verboseSyntax(): void
    {
        $this->config->set('HTML.AllowedElements', ['p', 'br']);
        $this->config->set('HTML.AllowedAttributes', '*@style');
        $this->assertPurification_AllowedAttributes_global_style();
    }

    /**
     * @test
     */
    public function test_AllowedAttributes_global_discouragedSyntax(): void
    {
        // Emit errors eventually
        $this->config->set('HTML.AllowedElements', ['p', 'br']);
        $this->config->set('HTML.AllowedAttributes', '*.style');
        $this->assertPurification_AllowedAttributes_global_style();
    }

    /**
     * @test
     */
    public function test_AllowedAttributes_local_preferredSyntax(): void
    {
        $this->config->set('HTML.AllowedElements', ['p', 'br']);
        $this->config->set('HTML.AllowedAttributes', 'p@style');
        $this->assertPurification_AllowedAttributes_local_p_style();
    }

    /**
     * @test
     */
    public function test_AllowedAttributes_local_discouragedSyntax(): void
    {
        $this->config->set('HTML.AllowedElements', ['p', 'br']);
        $this->config->set('HTML.AllowedAttributes', 'p.style');
        $this->assertPurification_AllowedAttributes_local_p_style();
    }

    /**
     * @test
     */
    public function test_AllowedAttributes_multiple(): void
    {
        $this->config->set('HTML.AllowedElements', ['p', 'br']);
        $this->config->set('HTML.AllowedAttributes', 'p@style,br@class,title');
        $this->assertPurification(
            '<p style="font-weight:bold;" class="foo" title="foo">Jelly</p><br style="clear:both;" class="foo" title="foo" />',
            '<p style="font-weight:bold;" title="foo">Jelly</p><br class="foo" title="foo" />'
        );
    }

    /**
     * @test
     */
    public function test_AllowedAttributes_local_invalidAttribute(): void
    {
        $this->config->set('HTML.AllowedElements', ['p', 'br']);
        $this->config->set('HTML.AllowedAttributes', ['p@style', 'p@<foo>']);

        $this->expectError();
        $this->expectErrorMessage("Attribute '&lt;foo&gt;' in element 'p' not supported");

        $this->assertPurification_AllowedAttributes_local_p_style();
    }

    /**
     * @test
     */
    public function test_AllowedAttributes_global_invalidAttribute(): void
    {
        $this->config->set('HTML.AllowedElements', ['p', 'br']);
        $this->config->set('HTML.AllowedAttributes', ['style', '<foo>']);

        $this->expectError();
        $this->expectErrorMessage("Global attribute '&lt;foo&gt;' is not supported in any elements");

        $this->assertPurification_AllowedAttributes_global_style();
    }

    /**
     * @test
     */
    public function test_AllowedAttributes_local_invalidAttributeDueToMissingElement(): void
    {
        $this->config->set('HTML.AllowedElements', ['p', 'br']);
        $this->config->set('HTML.AllowedAttributes', 'p.style,foo.style');
        
        $this->expectError();
        $this->expectErrorMessage("Cannot allow attribute 'style' if element 'foo' is not allowed/supported");
        
        $this->assertPurification_AllowedAttributes_local_p_style();
    }

    /**
     * @test
     */
    public function test_AllowedAttributes_duplicate(): void
    {
        $this->config->set('HTML.AllowedElements', ['p', 'br']);
        $this->config->set('HTML.AllowedAttributes', 'p.style,p@style');
        $this->assertPurification_AllowedAttributes_local_p_style();
    }

    /**
     * @test
     */
    public function test_AllowedAttributes_multipleErrors(): void
    {
        $this->config->set('HTML.AllowedElements', ['p', 'br']);
        $this->config->set('HTML.AllowedAttributes', 'p.style,foo.style,<foo>');

        $this->expectError();
        $this->expectErrorMessage("Cannot allow attribute 'style' if element 'foo' is not allowed/supported");

        $this->assertPurification_AllowedAttributes_local_p_style();
    }

    /**
     * @test
     */
    public function test_AllowedAttributes_multipleErrors2(): void
    {
        $this->config->set('HTML.AllowedElements', ['p', 'br']);
        $this->config->set('HTML.AllowedAttributes', '<foo>,p.style,foo.style');

        $this->expectError();
        $this->expectErrorMessage("Global attribute '&lt;foo&gt;' is not supported in any elements");

        $this->assertPurification_AllowedAttributes_local_p_style();
    }

    /**
     * @test
     */
    public function test_ForbiddenElements(): void
    {
        $this->config->set('HTML.ForbiddenElements', 'b');
        $this->assertPurification('<b>b</b><i>i</i>', 'b<i>i</i>');
    }

    /**
     * @test
     */
    public function test_ForbiddenElements_invalidElement(): void
    {
        $this->config->set('HTML.ForbiddenElements', 'obviously_incorrect');
        // no error!
        $this->assertPurification('<i>i</i>');
    }

    /**
     * @test
     */
    public function test_ForbiddenAttributes(): void
    {
        $this->config->set('HTML.ForbiddenAttributes', 'b@style');
        $this->assertPurification_ForbiddenAttributes_b_style();
    }

    /**
     * @test
     */
    public function test_ForbiddenAttributes_incorrectSyntax(): void
    {
        $this->config->set('HTML.ForbiddenAttributes', 'b.style');

        $this->expectError();
        $this->expectErrorMessage('Error with b.style: tag.attr syntax not supported for HTML.ForbiddenAttributes; use tag@attr instead');

        $this->assertPurification('<b style="float:left;">Test</b>');
    }

    /**
     * @test
     */
    public function test_ForbiddenAttributes_incorrectGlobalSyntax(): void
    {
        $this->config->set('HTML.ForbiddenAttributes', '*.style');

        $this->expectError();
        $this->expectErrorMessage('Error with *.style: *.attr syntax not supported for HTML.ForbiddenAttributes; use attr instead');

        $this->assertPurification('<b style="float:left;">Test</b>');
    }

    /**
     * @test
     */
    public function test_ForbiddenAttributes_global(): void
    {
        $this->config->set('HTML.ForbiddenAttributes', 'style');
        $this->assertPurification_ForbiddenAttributes_style();
    }

    /**
     * @test
     */
    public function test_ForbiddenAttributes_globalVerboseFormat(): void
    {
        $this->config->set('HTML.ForbiddenAttributes', '*@style');
        $this->assertPurification_ForbiddenAttributes_style();
    }

    /**
     * @test
     */
    public function test_addAttribute(): void
    {
        $config = HTMLPurifier_Config::createDefault();
        $def = $config->getHTMLDefinition(true);
        $def->addAttribute('span', 'custom', 'Enum#attribute');

        $purifier = new HTMLPurifier($config);
        $input = '<span custom="attribute">Custom!</span>';
        $output = $purifier->purify($input);

        static::assertEquals($input, $output);
    }

    /**
     * @test
     */
    public function test_addAttribute_multiple(): void
    {
        $config = HTMLPurifier_Config::createDefault();
        $def = $config->getHTMLDefinition(true);
        $def->addAttribute('span', 'custom', 'Enum#attribute');
        $def->addAttribute('span', 'foo', 'Text');

        $purifier = new HTMLPurifier($config);
        $input = '<span custom="attribute" foo="asdf">Custom!</span>';
        $output = $purifier->purify($input);

        static::assertEquals($input, $output);
    }

    /**
     * @test
     */
    public function test_addElement(): void
    {
        $config = HTMLPurifier_Config::createDefault();
        $def = $config->getHTMLDefinition(true);
        $def->addElement('marquee', 'Inline', 'Inline', 'Common', ['width' => 'Length']);

        $purifier = new HTMLPurifier($config);
        $input = '<span><marquee width="50">Foobar</marquee></span>';
        $output = $purifier->purify($input);

        static::assertEquals($input, $output);
    }

    /**
     * @test
     */
    public function test_injector(): void
    {
        $injector = \Mockery::mock(Injector::class);
        $injector->name = 'MyInjector';
        $injector->expects()
            ->checkNeeded($this->config)
            ->once()
            ->andReturn(false);

        $module = $this->config->getHTMLDefinition(true)->getAnonymousModule();
        $module->info_injector[] = $injector;

        static::assertEquals(
            [
                'MyInjector' => $injector,
            ],
            $this->config->getHTMLDefinition()->info_injector
        );
    }

    /**
     * @test
     */
    public function test_injectorMissingNeeded(): void
    {
        $injector = \Mockery::mock(Injector::class);
        $injector->name = 'MyInjector';
        $injector->expects()
            ->checkNeeded($this->config)
            ->once()
            ->andReturn('a');

        $module = $this->config->getHTMLDefinition(true)->getAnonymousModule();
        $module->info_injector[] = $injector;

        static::assertEquals(
            [],
            $this->config->getHTMLDefinition()->info_injector
        );
    }

    /**
     * @test
     */
    public function test_injectorIntegration(): void
    {
        $module = $this->config->getHTMLDefinition(true)->getAnonymousModule();
        $module->info_injector[] = 'Linkify';

        static::assertEquals(
            ['Linkify' => new HTMLPurifier_Injector_Linkify()],
            $this->config->getHTMLDefinition()->info_injector
        );
    }

    /**
     * @test
     */
    public function test_injectorIntegrationFail(): void
    {
        $this->config->set('HTML.Allowed', 'p');

        $module = $this->config->getHTMLDefinition(true)->getAnonymousModule();
        $module->info_injector[] = 'Linkify';

        static::assertEquals(
            [],
            $this->config->getHTMLDefinition()->info_injector
        );
    }

    /**
     * @test
     */
    public function test_notAllowedRequiredAttributeError(): void
    {
        $this->expectError();
        $this->expectErrorMessage("Required attribute 'src' in element 'img' was not allowed, which means 'img' will not be allowed either");

        $this->config->set('HTML.Allowed', 'img[alt]');
        $this->config->getHTMLDefinition();
    }

    /**
     * @test
     */
    public function test_manyNestedTags(): void
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Core.AllowParseManyTags', true);
        $purifier = new HTMLPurifier($config);

        $input = 'I am inside a lot of tags';
        for ($i = 0; $i < 300; $i++) {
            $input = '<div>' . $input . '</div>';
        }
        $output = $purifier->purify($input);

        static::assertEquals($input, $output);
    }

    public function expectError(): void
    {
        // Because we're testing a definition, it's vital that the cache
        // is turned off for tests that expect errors.
        $this->config->set('Cache.DefinitionImpl', null);
        parent::expectError();
    }

    /**
     * @param string $message
     */
    public function expectErrorMessage(string $message): void
    {
        // Because we're testing a definition, it's vital that the cache
        // is turned off for tests that expect errors.
        $this->config->set('Cache.DefinitionImpl', null);
        parent::expectErrorMessage($message);
    }

    /**
     * @throws \HTMLPurifier_Exception
     */
    private function assertPurification_AllowedElements_p(): void
    {
        $this->assertPurification('<p><b>Jelly</b></p>', '<p>Jelly</p>');
    }

    private function assertPurification_AllowedAttributes_global_style(): void
    {
        $this->assertPurification(
            '<p style="font-weight:bold;" class="foo">Jelly</p><br style="clear:both;" />',
            '<p style="font-weight:bold;">Jelly</p><br style="clear:both;" />');
    }

    private function assertPurification_AllowedAttributes_local_p_style(): void
    {
        $this->assertPurification(
            '<p style="font-weight:bold;" class="foo">Jelly</p><br style="clear:both;" />',
            '<p style="font-weight:bold;">Jelly</p><br />');
    }

    private function assertPurification_ForbiddenAttributes_b_style(): void
    {
        $this->assertPurification(
            '<b style="float:left;">b</b><i style="float:left;">i</i>',
            '<b>b</b><i style="float:left;">i</i>');
    }

    private function assertPurification_ForbiddenAttributes_style(): void
    {
        $this->assertPurification(
            '<b class="foo" style="float:left;">b</b><i style="float:left;">i</i>',
            '<b class="foo">b</b><i>i</i>');
    }
}
