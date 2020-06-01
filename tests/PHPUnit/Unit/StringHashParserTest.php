<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\ConfigSchema\Exception;
use HTMLPurifier\StringHashParser;

/**
 * Class StringHashParserTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class StringHashParserTest extends TestCase
{
    private const FILE_LOCATION = __DIR__ . '/../files/StringHashParser/';

    /**
     * Instance of ConfigSchema_StringHashParser being tested.
     */
    protected $parser;

    protected function setUp(): void
    {
        $this->parser = new StringHashParser();
    }

    /**
     * Assert that $file gets parsed into the form of $expect
     */
    private function assertParse($file, $expect): void
    {
        $result = $this->parser->parseFile(static::FILE_LOCATION . $file);
        static::assertEquals($expect, $result);
    }

    /**
     * @test
     */
    public function testSimple(): void
    {
        $this->assertParse('Simple.txt', [
            'ID' => 'Namespace.Directive',
            'TYPE' => 'string',
            'CHAIN-ME' => '2',
            'DESCRIPTION' => "Multiline\nstuff\n",
            'EMPTY' => '',
            'FOR-WHO' => "Single multiline\n",
        ]);
    }

    /**
     * @test
     */
    public function testOverrideSingle(): void
    {
        $this->assertParse('OverrideSingle.txt', [
            'KEY' => 'New',
        ]);
    }

    /**
     * @test
     */
    public function testAppendMultiline(): void
    {
        $this->assertParse('AppendMultiline.txt', [
            'KEY' => "Line1\nLine2\n",
        ]);
    }

    /**
     * @test
     */
    public function testDefault(): void
    {
        $this->parser->default = 'NEW-ID';
        $this->assertParse('Default.txt', [
            'NEW-ID' => 'DefaultValue',
        ]);
    }

    /**
     * @test
     */
    public function testError(): void
    {
        $this->markTestIncomplete('I think this test was broken in simpletest.');

        try {
            $this->parser->parseFile('NoExist.txt');
        } catch (Exception $e) {
            static::assertEquals('File NoExist.txt does not exist', $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function testParseMultiple(): void
    {
        $result = $this->parser->parseMultiFile(static::FILE_LOCATION . 'Multi.txt');
        static::assertEquals(
            [
                [
                    'ID' => 'Namespace.Directive',
                    'TYPE' => 'string',
                    'CHAIN-ME' => '2',
                    'DESCRIPTION' => "Multiline\nstuff\n",
                    'FOR-WHO' => "Single multiline\n",
                ],
                [
                    'ID' => 'Namespace.Directive2',
                    'TYPE' => 'integer',
                    'CHAIN-ME' => '3',
                    'DESCRIPTION' => "M\nstuff\n",
                    'FOR-WHO' => "Single multiline2\n",
                ]
            ],
            $result
        );
    }
}
