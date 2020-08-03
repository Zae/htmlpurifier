<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\VarParser;

use HTMLPurifier\VarParser;
use HTMLPurifier\VarParserException;

/**
 * Class FlexibleTest
 *
 * @package HTMLPurifier\Tests\Unit\VarParser
 */
class FlexibleTest extends TestCase
{
    protected function setUp(): void
    {
        $this->parser = new VarParser\Flexible();
        parent::setUp();
    }

    /**
     * @test
     */
    public function testValidate(): void
    {
        $this->assertValid('foobar', 'string');
        $this->assertValid('foobar', 'text');
        $this->assertValid('FOOBAR', 'istring', 'foobar');
        $this->assertValid('FOOBAR', 'itext', 'foobar');

        $this->assertValid(34, 'int');

        $this->assertValid(3.34, 'float');

        $this->assertValid(false, 'bool');
        $this->assertValid(0, 'bool', false);
        $this->assertValid(1, 'bool', true);
        $this->assertValid('true', 'bool', true);
        $this->assertValid('false', 'bool', false);
        $this->assertValid('1', 'bool', true);
        $this->assertInvalid(34, 'bool');
        $this->assertInvalid(null, 'bool');

        $this->assertValid(array('1', '2', '3'), 'list');
        $this->assertValid('foo,bar, cow', 'list', array('foo', 'bar', 'cow'));
        $this->assertValid('', 'list', array());
        $this->assertValid("foo\nbar", 'list', array('foo', 'bar'));
        $this->assertValid("foo\nbar,baz", 'list', array('foo', 'bar', 'baz'));

        $this->assertValid(array('1' => true, '2' => true), 'lookup');
        $this->assertValid(array('1', '2'), 'lookup', array('1' => true, '2' => true));
        $this->assertValid('foo,bar', 'lookup', array('foo' => true, 'bar' => true));
        $this->assertValid("foo\nbar", 'lookup', array('foo' => true, 'bar' => true));
        $this->assertValid("foo\nbar,baz", 'lookup', array('foo' => true, 'bar' => true, 'baz' => true));
        $this->assertValid('', 'lookup', array());
        $this->assertValid(array(), 'lookup');

        $this->assertValid(array('foo' => 'bar'), 'hash');
        $this->assertValid(array(1 => 'moo'), 'hash');
        $this->assertInvalid(array(0 => 'moo'), 'hash');
        $this->assertValid('', 'hash', array());
        $this->assertValid('foo:bar,too:two', 'hash', array('foo' => 'bar', 'too' => 'two'));
        $this->assertValid("foo:bar\ntoo:two,three:free", 'hash', array('foo' => 'bar', 'too' => 'two', 'three' => 'free'));
        $this->assertValid('foo:bar,too', 'hash', array('foo' => 'bar'));
        $this->assertValid('foo:bar,', 'hash', array('foo' => 'bar'));
        $this->assertValid('foo:bar:baz', 'hash', array('foo' => 'bar:baz'));

        $this->assertValid(23, 'mixed');
    }

    /**
     * @test
     */
    public function testValidate_withMagicNumbers(): void
    {
        $this->assertValid('foobar', VarParser::C_STRING);
    }

    /**
     * @test
     */
    public function testValidate_null(): void
    {
        static::assertNull($this->parser->parse(null, 'string', true));

        $this->expectException(VarParserException::class);
        $this->parser->parse(null, 'string', false);
    }

    /**
     * @test
     * @throws VarParserException
     * @throws \HTMLPurifier\Exception
     */
    public function testInvalidBool(): void
    {
        $this->expectException(VarParserException::class);
        $this->expectExceptionMessage('Unrecognized value \'a\' for 7');
        $this->parser->parse('a', VarParser::C_BOOL, true);
    }

    /**
     * @test
     * @throws VarParserException
     * @throws \HTMLPurifier\Exception
     */
    public function testInvalidArray(): void
    {
        $this->expectException(VarParserException::class);
        $this->expectExceptionMessage('Expected type lookup, got int');

        $this->parser->parse(2, VarParser::LOOKUP, true);
    }

    /**
     * @test
     * @throws VarParserException
     * @throws \HTMLPurifier\Exception
     */
    public function testInvalidFlexibleAlist(): void
    {
        $this->expectError();
        $this->expectErrorMessage('Array list did not have consecutive integer indexes');

        $this->parser->parse(['b' => true, 'd' => false], VarParser::ALIST, true);
    }

    /**
     * @test
     */
    public function testInvalidFlexibleLookup(): void
    {
        $this->expectError();
        $this->expectErrorMessage("Lookup array has non-true value at key 'a'; " .
                                  'maybe your input array was not indexed numerically');
        $this->parser->parse(["a" => false], VarParser::LOOKUP, true);
    }

    /**
     * @test
     */
    public function testInvalidFlexibleUnknown(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Inconsistency in HTMLPurifier\VarParser\Flexible: unknown not implemented');
        $this->parser->parse('a', 999999, true);
    }
}
