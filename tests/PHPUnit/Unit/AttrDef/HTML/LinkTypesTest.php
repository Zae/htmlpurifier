<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef\HTML;

use HTMLPurifier\AttrDef\HTML\LinkTypes;
use HTMLPurifier\Tests\Unit\AttrDef\TestCase;

/**
 * Class LinkTypesTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\HTML
 */
class LinkTypesTest extends TestCase
{
    /**
     * @test
     */
    public function testNull(): void
    {
        $this->def = new LinkTypes('rel');
        $this->config->set('Attr.AllowedRel', ['nofollow', 'foo']);

        $this->assertDef('', false);
        $this->assertDef('nofollow', true);
        $this->assertDef('nofollow foo', true);
        $this->assertDef('nofollow bar', 'nofollow');
        $this->assertDef('bar', false);
    }
}
