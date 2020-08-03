<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\VarParser;

use HTMLPurifier\VarParser;
use HTMLPurifier\VarParserException;

/**
 * Class NativeTest
 *
 * @package HTMLPurifier\Tests\Unit\VarParser
 */
class NativeTest extends TestCase
{
    protected function setUp(): void
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

    /**
     * @test
     */
    public function testEvalReturnsFalse(): void
    {
        $this->expectException(VarParserException::class);
        $this->expectExceptionMessage('Fatal error in evaluated code');

        $this->parser->parse('\'a\';return false', VarParser::C_BOOL);
    }
}
