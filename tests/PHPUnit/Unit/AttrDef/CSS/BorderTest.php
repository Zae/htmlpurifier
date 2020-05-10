<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef\CSS;

use HTMLPurifier\AttrDef\CSS\Border;
use HTMLPurifier\Config;
use HTMLPurifier\Tests\Unit\AttrDef\TestCase;

/**
 * Class BorderTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\CSS
 */
class BorderTest extends TestCase
{
    /**
     * @test
     */
    public function test(): void
    {
        $config = Config::createDefault();
        $this->def = new Border($config);

        $this->assertDef('thick solid red', 'thick solid #FF0000');
        $this->assertDef('thick solid');
        $this->assertDef('solid red', 'solid #FF0000');
        $this->assertDef('1px solid #000');
        $this->assertDef('1px solid rgb(0, 0, 0)', '1px solid rgb(0,0,0)');
    }
}
