<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef\CSS;

use HTMLPurifier\AttrDef\CSS\Background;
use HTMLPurifier\Config;
use HTMLPurifier\Tests\Unit\AttrDef\TestCase;

/**
 * Class BackgroundTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\CSS
 */
class BackgroundTest extends TestCase
{
    /**
     * @test
     */
    public function test(): void
    {
        $config = Config::createDefault();
        $this->def = new Background($config);

        $valid = '#333 url("chess.png") repeat fixed 50% top';
        $this->assertDef($valid);
        $this->assertDef('url(\'chess.png\') #333 50% top repeat fixed', $valid);
        $this->assertDef(
            'rgb(34%, 56%, 33%) url(chess.png) repeat fixed top',
            'rgb(34%,56%,33%) url("chess.png") repeat fixed top'
        );
        $this->assertDef(
            'rgba(74, 12, 85, 0.35) repeat fixed bottom',
            'rgba(74,12,85,0.35) repeat fixed bottom'
        );
        $this->assertDef(
            'hsl(244, 47.4%, 88.1%) right center',
            'hsl(244,47.4%,88.1%) right center'
        );
    }
}
