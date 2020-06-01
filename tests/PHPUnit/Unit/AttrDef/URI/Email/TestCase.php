<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef\URI\Email;

/**
 * Class TestCase
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\URI\Email
 */
abstract class TestCase extends \HTMLPurifier\Tests\Unit\AttrDef\TestCase
{
    /**
     * @test
     * Tests common email strings that are obviously pass/fail
     */
    public function testCore(): void
    {
        $this->assertDef('bob@example.com');
        $this->assertDef('  bob@example.com  ', 'bob@example.com');
        $this->assertDef('bob.thebuilder@example.net');
        $this->assertDef('Bob_the_Builder-the-2nd@example.org');
        $this->assertDef('Bob%20the%20Builder@white-space.test');

        // extended format, with real name
        //$this->assertDef('Bob%20Builder%20%3Cbobby.bob.bob@it.is.example.com%3E');
        //$this->assertDef('Bob Builder <bobby.bob.bob@it.is.example.com>');

        // time to fail
        $this->assertDef('bob', false);
        $this->assertDef('bob@home@work', false);
        $this->assertDef('@example.com', false);
        $this->assertDef('bob@', false);
        $this->assertDef('', false);
    }
}
