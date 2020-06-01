<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\HTMLModule;

/**
 * Class PropietaryTest
 *
 * @package HTMLPurifier\Tests\Unit\HTMLModule
 */
class PropietaryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->config->set('HTML.Proprietary', true);
    }

    /**
     * @test
     */
    public function testMarquee(): void
    {
        $this->assertResult(
            '<span><marquee
                width="20%"
                height="34"
                direction="left"
                behavior="alternate"
                scrolldelay="3"
                scrollamount="5"
                loop="4"
                bgcolor="#FF0000"
                hspace="5"
                vspace="3"
                ><div>Block</div><span>Inline</span>Text</marquee></span>'
        );
    }
}
