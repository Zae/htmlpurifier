<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef\CSS;

use HTMLPurifier\AttrDef\CSS\Filter;
use HTMLPurifier\Tests\Unit\AttrDef\TestCase;

/**
 * Class FilterTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\CSS
 */
class FilterTest extends TestCase
{
    /**
     * @test
     */
    public function test(): void
    {
        $this->def = new Filter();

        $this->assertDef('none');

        $this->assertDef('alpha(opacity=0)');
        $this->assertDef('alpha(opacity=100)');
        $this->assertDef('alpha(opacity=50)');
        $this->assertDef('alpha(opacity=342)', 'alpha(opacity=100)');
        $this->assertDef('alpha(opacity=-23)', 'alpha(opacity=0)');

        $this->assertDef('alpha ( opacity = 0 )', 'alpha(opacity=0)');
        $this->assertDef('alpha(opacity=0,opacity=100)', 'alpha(opacity=0)');

        $this->assertDef('progid:DXImageTransform.Microsoft.Alpha(opacity=20)');

        $this->assertDef('progid:DXImageTransform.Microsoft.BasicImage(rotation=2, mirror=1)', false);
    }
}
