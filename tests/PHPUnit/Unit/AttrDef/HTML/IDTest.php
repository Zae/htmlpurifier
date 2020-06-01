<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef\HTML;

use HTMLPurifier\AttrDef\HTML\ID;
use HTMLPurifier\IDAccumulator;
use HTMLPurifier\Tests\Unit\AttrDef\TestCase;

/**
 * Class IDTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\HTML
 */
class IDTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $id_accumulator = new IDAccumulator();
        $this->context->register('IDAccumulator', $id_accumulator);
        $this->config->set('Attr.EnableID', true);
        $this->def = new ID();
    }

    /**
     * @test
     */
    public function test(): void
    {
        // valid ID names
        $this->assertDef('alpha');
        $this->assertDef('al_ha');
        $this->assertDef('a0-:.');
        $this->assertDef('a');

        // invalid ID names
        $this->assertDef('<asa', false);
        $this->assertDef('0123', false);
        $this->assertDef('.asa', false);

        // test duplicate detection
        $this->assertDef('once');
        $this->assertDef('once', false);

        // valid once whitespace stripped, but needs to be amended
        $this->assertDef(' whee ', 'whee');
    }

    /**
     * @test
     */
    public function testPrefix(): void
    {
        $this->config->set('Attr.IDPrefix', 'user_');

        $this->assertDef('alpha', 'user_alpha');
        $this->assertDef('<asa', false);
        $this->assertDef('once', 'user_once');
        $this->assertDef('once', false);

        // if already prefixed, leave alone
        $this->assertDef('user_alas');
        $this->assertDef('user_user_alas'); // how to bypass
    }

    /**
     * @test
     */
    public function testTwoPrefixes(): void
    {
        $this->config->set('Attr.IDPrefix', 'user_');
        $this->config->set('Attr.IDPrefixLocal', 'story95_');

        $this->assertDef('alpha', 'user_story95_alpha');
        $this->assertDef('<asa', false);
        $this->assertDef('once', 'user_story95_once');
        $this->assertDef('once', false);

        $this->assertDef('user_story95_alas');
        $this->assertDef('user_alas', 'user_story95_user_alas'); // !
    }

    /**
     * @test
     */
    public function testLocalPrefixWithoutMainPrefix(): void
    {
        // no effect when IDPrefix isn't set
        $this->config->set('Attr.IDPrefix', '');
        $this->config->set('Attr.IDPrefixLocal', 'story95_');
        $this->expectError('%Attr.IDPrefixLocal cannot be used unless %Attr.IDPrefix is set');
        $this->assertDef('amherst');
    }

    /**
     * @test
     */
    public function testIDReference(): void
    {
        $this->def = new ID(true);

        $this->assertDef('good_id');
        $this->assertDef('good_id'); // duplicates okay
        $this->assertDef('<b>', false);

        $this->def = new ID();

        $this->assertDef('good_id');
        $this->assertDef('good_id', false); // duplicate now not okay

        $this->def = new ID(true);

        $this->assertDef('good_id'); // reference still okay
    }

    /**
     * @test
     */
    public function testRegexp(): void
    {
        $this->config->set('Attr.IDBlacklistRegexp', '/^g_/');

        $this->assertDef('good_id');
        $this->assertDef('g_bad_id', false);
    }

    /**
     * @test
     */
    public function testRelaxed(): void
    {
        $this->config->set('Attr.ID.HTML5', true);

        $this->assertDef('123');
        $this->assertDef('x[1]');
        $this->assertDef('not ok', false);
        $this->assertDef(' ', false);
        $this->assertDef('', false);
    }
}
