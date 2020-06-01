<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Injector;

/**
 * Class PurifierLinkifyTest
 *
 * @package HTMLPurifier\Tests\Unit\Injector
 */
class PurifierLinkifyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->config->set('AutoFormat.PurifierLinkify', true);
        $this->config->set('AutoFormat.PurifierLinkify.DocURL', '#%s');
    }

    /**
     * @test
     */
    public function testNoTriggerCharacer(): void
    {
        $this->assertResult('Foobar');
    }

    /**
     * @test
     */
    public function testTriggerCharacterInIrrelevantContext(): void
    {
        $this->assertResult('20% off!');
    }

    /**
     * @test
     */
    public function testPreserveNamespace(): void
    {
        $this->assertResult('%Core namespace (not recognized)');
    }

    /**
     * @test
     */
    public function testLinkifyBasic(): void
    {
        $this->assertResult(
            '%Namespace.Directive',
            '<a href="#Namespace.Directive">%Namespace.Directive</a>'
        );
    }

    /**
     * @test
     */
    public function testLinkifyWithAdjacentTextNodes(): void
    {
        $this->assertResult(
            'This %Namespace.Directive thing',
            'This <a href="#Namespace.Directive">%Namespace.Directive</a> thing'
        );
    }

    /**
     * @test
     */
    public function testLinkifyInBlock(): void
    {
        $this->assertResult(
            '<div>This %Namespace.Directive thing</div>',
            '<div>This <a href="#Namespace.Directive">%Namespace.Directive</a> thing</div>'
        );
    }

    /**
     * @test
     */
    public function testPreserveInATag(): void
    {
        $this->assertResult(
            '<a>%Namespace.Directive</a>'
        );
    }

    /**
     * @test
     */
    public function testNeeded(): void
    {
        $this->config->set('HTML.Allowed', 'b');
        $this->expectError();
        $this->expectErrorMessage('Cannot enable PurifierLinkify injector because a is not allowed');
        $this->assertResult('%Namespace.Directive');
    }
}
