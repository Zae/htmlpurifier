<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\Encoder;
use HTMLPurifier\EntityLookup;
use HTMLPurifier\Exception;

/**
 * Class EncoderTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class EncoderTest extends TestCase
{
    private $_entity_lookup;

    protected function setUp(): void
    {
        $this->_entity_lookup = EntityLookup::instance();
        parent::setUp();
    }

    /**
     * @param      $string
     * @param null $expect
     */
    private function assertCleanUTF8($string, $expect = null): void
    {
        if ($expect === null) {
            $expect = $string;
        }

        static::assertEquals(Encoder::cleanUTF8($string), $expect, 'iconv: %s');
        static::assertEquals(Encoder::cleanUTF8($string, true), $expect, 'PHP: %s');
    }

    /**
     * @test
     */
    public function test_cleanUTF8(): void
    {
        $this->assertCleanUTF8('Normal string.');
        $this->assertCleanUTF8("Test\tAllowed\nControl\rCharacters");
        $this->assertCleanUTF8("null byte: \0", 'null byte: ');
        $this->assertCleanUTF8("あ（い）う（え）お\0", "あ（い）う（え）お"); // test for issue #122
        $this->assertCleanUTF8("\1\2\3\4\5\6\7", '');
        $this->assertCleanUTF8("\x7F", ''); // one byte invalid SGML char
        $this->assertCleanUTF8("\xC2\x80", ''); // two byte invalid SGML
        $this->assertCleanUTF8("\xF3\xBF\xBF\xBF"); // valid four byte
        $this->assertCleanUTF8("\xDF\xFF", ''); // malformed UTF8
        // invalid codepoints
        $this->assertCleanUTF8("\xED\xB0\x80", '');
    }

    /**
     * @test
     */
    public function test_convertToUTF8_noConvert(): void
    {
        // UTF-8 means that we don't touch it
        static::assertEquals(
            "\xF6",
            Encoder::convertToUTF8("\xF6", $this->config, $this->context), // this is invalid
            'Expected identical [Binary: F6]'
        );
    }

    /**
     * @test
     */
    public function test_convertToUTF8_spuriousEncoding(): void
    {
        if (!Encoder::iconvAvailable()) {
            return;
        }

        $this->config->set('Core.Encoding', 'utf99');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid encoding utf99');
        static::assertEquals(
            '',
            Encoder::convertToUTF8("\xF6", $this->config, $this->context)
        );
    }

    /**
     * @test
     */
    public function test_convertToUTF8_iso8859_1(): void
    {
        $this->config->set('Core.Encoding', 'ISO-8859-1');
        static::assertEquals(
            "\xC3\xB6",
            Encoder::convertToUTF8("\xF6", $this->config, $this->context)
        );
    }

    /**
     * @test
     */
    public function test_convertToUTF8_withoutIconv(): void
    {
        $this->config->set('Core.Encoding', 'ISO-8859-1');
        $this->config->set('Test.ForceNoIconv', true);
        static::assertEquals(
            "\xC3\xB6",
            Encoder::convertToUTF8("\xF6", $this->config, $this->context)
        );
    }

    private function getZhongWen(): string
    {
        return "\xE4\xB8\xAD\xE6\x96\x87 (Chinese)";
    }

    /**
     * @test
     */
    public function test_convertFromUTF8_utf8(): void
    {
        // UTF-8 means that we don't touch it
        static::assertEquals(
            "\xC3\xB6",
            Encoder::convertFromUTF8("\xC3\xB6", $this->config, $this->context)
        );
    }

    /**
     * @test
     */
    public function test_convertFromUTF8_iso8859_1(): void
    {
        $this->config->set('Core.Encoding', 'ISO-8859-1');
        static::assertEquals(
            "\xF6",
            Encoder::convertFromUTF8("\xC3\xB6", $this->config, $this->context),
            'Expected identical [Binary: F6]'
        );
    }

    /**
     * @test
     */
    public function test_convertFromUTF8_iconvNoChars(): void
    {
        if (!Encoder::iconvAvailable()) {
            return;
        }

        $this->config->set('Core.Encoding', 'ISO-8859-1');
        static::assertEquals(
            " (Chinese)",
            Encoder::convertFromUTF8($this->getZhongWen(), $this->config, $this->context)
        );
    }

    /**
     * @test
     */
    public function test_convertFromUTF8_phpNormal(): void
    {
        // Plain PHP implementation has slightly different behavior
        $this->config->set('Core.Encoding', 'ISO-8859-1');
        $this->config->set('Test.ForceNoIconv', true);
        static::assertEquals(
            "\xF6",
            Encoder::convertFromUTF8("\xC3\xB6", $this->config, $this->context),
            'Expected identical [Binary: F6]'
        );
    }

    /**
     * @test
     */
    public function test_convertFromUTF8_phpNoChars(): void
    {
        $this->config->set('Core.Encoding', 'ISO-8859-1');
        $this->config->set('Test.ForceNoIconv', true);
        static::assertEquals(
            "?? (Chinese)",
            Encoder::convertFromUTF8($this->getZhongWen(), $this->config, $this->context)
        );
    }

    /**
     * @test
     */
    public function test_convertFromUTF8_withProtection(): void
    {
        // Preserve the characters!
        $this->config->set('Core.Encoding', 'ISO-8859-1');
        $this->config->set('Core.EscapeNonASCIICharacters', true);
        static::assertEquals(
            "&#20013;&#25991; (Chinese)",
            Encoder::convertFromUTF8($this->getZhongWen(), $this->config, $this->context)
        );
    }

    /**
     * @test
     */
    public function test_convertFromUTF8_withProtectionButUtf8(): void
    {
        // Preserve the characters!
        $this->config->set('Core.EscapeNonASCIICharacters', true);
        static::assertEquals(
            "&#20013;&#25991; (Chinese)",
            Encoder::convertFromUTF8($this->getZhongWen(), $this->config, $this->context)
        );
    }

    /**
     * @test
     */
    public function test_convertToASCIIDumbLossless(): void
    {
        // Uppercase thorn letter
        $this->assertEquals(
            Encoder::convertToASCIIDumbLossless("\xC3\x9Eorn"),
            "&#222;orn"
        );

        static::assertEquals(
            "an",
            Encoder::convertToASCIIDumbLossless("an")
        );

        // test up to four bytes
        static::assertEquals(
            "&#917536;",
            Encoder::convertToASCIIDumbLossless("\xF3\xA0\x80\xA0")
        );
    }

    /**
     * @param $enc
     * @param $ret
     */
    private function assertASCIISupportCheck($enc, $ret): void
    {
        $test = Encoder::testEncodingSupportsASCII($enc, true);
        if ($test === false) {
            return;
        }

        static::assertEquals(
            $ret,
            Encoder::testEncodingSupportsASCII($enc)
        );
        
        static::assertEquals(
            $ret,
            Encoder::testEncodingSupportsASCII($enc, true)
        );
    }

    /**
     * @test
     */
    public function test_testEncodingSupportsASCII(): void
    {
        if (Encoder::iconvAvailable()) {
            $this->assertASCIISupportCheck('Shift_JIS', ["\xC2\xA5" => '\\', "\xE2\x80\xBE" => '~']);
            $this->assertASCIISupportCheck('JOHAB', ["\xE2\x82\xA9" => '\\']);
        }

        $this->assertASCIISupportCheck('ISO-8859-1', []);
        $this->assertASCIISupportCheck('dontexist', []); // canary
    }

    /**
     * @test
     */
    public function testShiftJIS(): void
    {
        if (!Encoder::iconvAvailable()) {
            return;
        }

        $this->config->set('Core.Encoding', 'Shift_JIS');
        // This actually looks like a Yen, but we're going to treat it differently
        static::assertEquals(
            '\\~',
            Encoder::convertFromUTF8('\\~', $this->config, $this->context)
        );
        static::assertEquals(
            '\\~',
            Encoder::convertToUTF8('\\~', $this->config, $this->context)
        );
    }

    /**
     * @test
     */
    public function testIconvTruncateBug(): void
    {
        if (!Encoder::iconvAvailable()) {
            static::markTestSkipped('iconv not available');
            return;
        }

        if (Encoder::testIconvTruncateBug() !== Encoder::ICONV_TRUNCATES) {
            static::markTestSkipped('IconvTruncateBug');
            return;
        }

        $this->config->set('Core.Encoding', 'ISO-8859-1');
        static::assertEquals(
            str_repeat('a', 10000),
            Encoder::convertFromUTF8("\xE4\xB8\xAD" . str_repeat('a', 10000), $this->config, $this->context)
        );
    }

    /**
     * @test
     */
    public function testIconvChunking(): void
    {
        if (!Encoder::iconvAvailable()) {
            static::markTestSkipped('iconv not available');
            return;
        }

        if (Encoder::testIconvTruncateBug() !== Encoder::ICONV_TRUNCATES) {
            static::markTestSkipped('IconvTruncateBug');
            return;
        }

        static::assertEquals('ab', Encoder::iconv('utf-8', 'iso-8859-1//IGNORE', "a\xF3\xA0\x80\xA0b", 4));
        static::assertEquals('aab', Encoder::iconv('utf-8', 'iso-8859-1//IGNORE', "aa\xE4\xB8\xADb", 4));
        static::assertEquals('aaab', Encoder::iconv('utf-8', 'iso-8859-1//IGNORE', "aaa\xCE\xB1b", 4));
        static::assertEquals('aaaab', Encoder::iconv('utf-8', 'iso-8859-1//IGNORE', "aaaa\xF3\xA0\x80\xA0b", 4));
        static::assertEquals('aaaab', Encoder::iconv('utf-8', 'iso-8859-1//IGNORE', "aaaa\xE4\xB8\xADb", 4));
        static::assertEquals('aaaab', Encoder::iconv('utf-8', 'iso-8859-1//IGNORE', "aaaa\xCE\xB1b", 4));
    }
}
