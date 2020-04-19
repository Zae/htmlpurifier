<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\VarParser;

use HTMLPurifier\VarParser;

/**
 * Class NativeTest
 *
 * @package HTMLPurifier\Tests\Unit\VarParser
 */
class NativeTest extends TestCase
{
    public function setUp(): void
    {
        $this->parser = new VarParser\Native();
        parent::setUp();
    }

    /**
     * @test
     */
    public function testValidateSimple(): void
    {
        $this->assertValid('"foo\\\\"', 'string', 'foo\\');
    }
}
