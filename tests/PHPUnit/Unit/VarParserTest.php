<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use Exception;
use HTMLPurifier\VarParser;
use HTMLPurifier\VarParserException;

/**
 * Class VarParserTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class VarParserTest extends TestCase
{
    /**
     * @test
     */
    public function testInvalidFloat(): void
    {
        $parser = new VarParser();

        $this->expectException(VarParserException::class);
        $this->expectExceptionMessage('Expected type float, got string');
        $parser->parse("a", VarParser::C_FLOAT, true);
    }

    /**
     * @test
     */
    public function testInvalidHash(): void
    {
        $parser = new VarParser();

        $this->expectException(VarParserException::class);
        $this->expectExceptionMessage('Expected type hash, got string');
        $parser->parse("a", VarParser::HASH, true);
    }

    /**
     * @test
     */
    public function testInvalidLookup(): void
    {
        $parser = new VarParser();

        $this->expectException(VarParserException::class);
        $this->expectExceptionMessage('Lookup table contains value other than true');
        $parser->parse(["a" => false], VarParser::LOOKUP, true);
    }

    /**
     * @test
     */
    public function testInvalidAlist(): void
    {
        $parser = new VarParser();

        $this->expectException(VarParserException::class);
        $this->expectExceptionMessage('Indices for list are not uniform');
        $parser->parse(["a" => false, 2 => ''], VarParser::ALIST, true);
    }

    /**
     * @test
     */
    public function testInvalidUnknown(): void
    {
        $parser = new VarParser();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Inconsistency in HTMLPurifier\VarParser: unknown not implemented');
        $parser->parse('a', 999999, true);
    }
}
