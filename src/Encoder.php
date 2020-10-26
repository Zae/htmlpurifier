<?php

declare(strict_types=1);

namespace HTMLPurifier;

use function function_exists;
use function ord;
use function strlen;

/**
 * A UTF-8 specific character encoder that handles cleaning and transforming.
 *
 * @note All functions in this class should be static.
 */
class Encoder
{
    /** @var ?int */
    protected static $iconvCode = null;

    /** @var ?bool  */
    protected static $iconvAvailable = null;

    /**
     * @var array
     */
    protected static $asciiEncodings = [];

    /**
     * Constructor throws fatal error if you attempt to instantiate class
     *
     * @throws Exception
     */
    private function __construct()
    {
        throw new Exception('Cannot instantiate encoder, call methods statically');
    }

    /**
     * Error-handler that mutes errors, alternative to shut-up operator.
     */
    public static function muteErrorHandler(): void
    {
    }

    /**
     * iconv wrapper which mutes errors, but doesn't work around bugs.
     *
     * @param string $in   Input encoding
     * @param string $out  Output encoding
     * @param string $text The text to convert
     *
     * @return string|null
     */
    public static function unsafeIconv(string $in, string $out, string $text): ?string
    {
        /**
         * @psalm-suppress InvalidArgument
         * @phpstan-ignore-next-line
         * @todo fix? Psalm/PHPstan doesn't seem to understand array callbacks?
         */
        set_error_handler([__CLASS__, 'muteErrorHandler']);
        $r = iconv($in, $out, $text);
        restore_error_handler();

        if ($r === false) {
            return null;
        }

        return $r;
    }

    /**
     * iconv wrapper which mutes errors and works around bugs.
     *
     * @param string $in   Input encoding
     * @param string $out  Output encoding
     * @param string $text The text to convert
     * @param int    $max_chunk_size
     *
     * @return string|null
     */
    public static function iconv(string $in, string $out, string $text, int $max_chunk_size = 8000): ?string
    {
        $code = static::testIconvTruncateBug();
        if ($code === static::ICONV_OK) {
            return static::unsafeIconv($in, $out, $text);
        }

        // we can only work around this if the input character set
        // is utf-8
        if (($code === static::ICONV_TRUNCATES) && $in === 'utf-8') {
            if ($max_chunk_size < 4) {
                Log::warning('max_chunk_size is too small');
                return null;
            }

            // split into 8000 byte chunks, but be careful to handle
            // multibyte boundaries properly
            if (($c = strlen($text)) <= $max_chunk_size) {
                return static::unsafeIconv($in, $out, $text);
            }

            $r = '';
            $i = 0;

            while (true) {
                if ($i + $max_chunk_size >= $c) {
                    $r .= static::unsafeIconv($in, $out, substr($text, $i));
                    break;
                }

                // wibble the boundary
                if ((0xC0 & ord($text[$i + $max_chunk_size])) !== 0x80) {
                    $chunk_size = $max_chunk_size;
                } elseif ((0xC0 & ord($text[$i + $max_chunk_size - 1])) !== 0x80) {
                    $chunk_size = $max_chunk_size - 1;
                } elseif ((0xC0 & ord($text[$i + $max_chunk_size - 2])) !== 0x80) {
                    $chunk_size = $max_chunk_size - 2;
                } elseif ((0xC0 & ord($text[$i + $max_chunk_size - 3])) !== 0x80) {
                    $chunk_size = $max_chunk_size - 3;
                } else {
                    return null; // rather confusing UTF-8...
                }

                $chunk = substr($text, $i, $chunk_size); // substr doesn't mind overlong lengths
                $r .= static::unsafeIconv($in, $out, $chunk);
                $i += $chunk_size;
            }

            return $r;
        }

        return null;
    }

    /**
     * Cleans a UTF-8 string for well-formedness and SGML validity
     *
     * It will parse according to UTF-8 and return a valid UTF8 string, with
     * non-SGML codepoints excluded.
     *
     * Specifically, it will permit:
     * \x{9}\x{A}\x{D}\x{20}-\x{7E}\x{A0}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}
     * Source: https://www.w3.org/TR/REC-xml/#NT-Char
     * Arguably this function should be modernized to the HTML5 set
     * of allowed characters:
     * https://www.w3.org/TR/html5/syntax.html#preprocessing-the-input-stream
     * which simultaneously expand and restrict the set of allowed characters.
     *
     * @param string $str The string to clean
     * @param bool   $force_php
     *
     * @return string
     *
     * @note Just for reference, the non-SGML code points are 0 to 31 and
     *       127 to 159, inclusive.  However, we allow code points 9, 10
     *       and 13, which are the tab, line feed and carriage return
     *       respectively. 128 and above the code points map to multibyte
     *       UTF-8 representations.
     *
     * @note Fallback code adapted from utf8ToUnicode by Henri Sivonen and
     *       hsivonen@iki.fi at <http://iki.fi/hsivonen/php-utf8/> under the
     *       LGPL license.  Notes on what changed are inside, but in general,
     *       the original code transformed UTF-8 text into an array of integer
     *       Unicode codepoints. Understandably, transforming that back to
     *       a string would be somewhat expensive, so the function was modded to
     *       directly operate on the string.  However, this discourages code
     *       reuse, and the logic enumerated here would be useful for any
     *       function that needs to be able to understand UTF-8 characters.
     *       As of right now, only smart lossless character encoding converters
     *       would need that, and I'm probably not going to implement them.
     */
    public static function cleanUTF8(string $str, bool $force_php = false): string
    {
        // UTF-8 validity is checked since PHP 4.3.5
        // This is an optimization: if the string is already valid UTF-8, no
        // need to do PHP stuff. 99% of the time, this will be the case.
        if (
            preg_match(
                '/^[\x{9}\x{A}\x{D}\x{20}-\x{7E}\x{A0}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]*$/Du',
                $str
            )
        ) {
            return $str;
        }

        $mState = 0; // cached expected number of octets after the current octet
        // until the beginning of the next UTF8 character sequence
        $mUcs4 = 0; // cached Unicode character
        $mBytes = 1; // cached expected number of octets in the current sequence

        // original code involved an $out that was an array of Unicode
        // codepoints.  Instead of having to convert back into UTF-8, we've
        // decided to directly append valid UTF-8 characters onto a string
        // $out once they're done.  $char accumulates raw bytes, while $mUcs4
        // turns into the Unicode code point, so there's some redundancy.

        $out = '';
        $char = '';

        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            $in = ord($str[$i]);
            $char .= $str[$i]; // append byte to char
            if ($mState === 0) {
                // When mState is zero we expect either a US-ASCII character
                // or a multi-octet sequence.
                if ((0x80 & ($in)) === 0) {
                    // US-ASCII, pass straight through.
                    if (
                        ($in <= 31 || $in === 127)
                        && !($in === 9 || $in === 13 || $in === 10) // save \r\t\n
                    ) {
                        // control characters, remove
                    } else {
                        $out .= $char;
                    }
                    // reset
                    $char = '';
                    $mBytes = 1;
                } elseif ((0xE0 & ($in)) === 0xC0) {
                    // First octet of 2 octet sequence
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 0x1F) << 6;
                    $mState = 1;
                    $mBytes = 2;
                } elseif ((0xF0 & ($in)) === 0xE0) {
                    // First octet of 3 octet sequence
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 0x0F) << 12;
                    $mState = 2;
                    $mBytes = 3;
                } elseif ((0xF8 & ($in)) === 0xF0) {
                    // First octet of 4 octet sequence
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 0x07) << 18;
                    $mState = 3;
                    $mBytes = 4;
                } elseif ((0xFC & ($in)) === 0xF8) {
                    // First octet of 5 octet sequence.
                    //
                    // This is illegal because the encoded codepoint must be
                    // either:
                    // (a) not the shortest form or
                    // (b) outside the Unicode range of 0-0x10FFFF.
                    // Rather than trying to resynchronize, we will carry on
                    // until the end of the sequence and let the later error
                    // handling code catch it.
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 0x03) << 24;
                    $mState = 4;
                    $mBytes = 5;
                } elseif ((0xFE & ($in)) === 0xFC) {
                    // First octet of 6 octet sequence, see comments for 5
                    // octet sequence.
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 1) << 30;
                    $mState = 5;
                    $mBytes = 6;
                } else {
                    // Current octet is neither in the US-ASCII range nor a
                    // legal first octet of a multi-octet sequence.
                    $mState = 0;
                    $mUcs4 = 0;
                    $mBytes = 1;
                    $char = '';
                }
            } elseif ((0xC0 & ($in)) === 0x80) {
                // Legal continuation.
                $shift = ($mState - 1) * 6;
                $tmp = $in;
                $tmp = ($tmp & 0x0000003F) << $shift;
                $mUcs4 |= $tmp;

                if (--$mState === 0) {
                    // End of the multi-octet sequence. mUcs4 now contains
                    // the final Unicode codepoint to be output

                    // Check for illegal sequences and codepoints.

                    // From Unicode 3.1, non-shortest form is illegal
                    if (
                        (($mBytes === 2) && ($mUcs4 < 0x0080))
                        || (($mBytes === 3) && ($mUcs4 < 0x0800))
                        || (($mBytes === 4) && ($mUcs4 < 0x10000))
                        || (4 < $mBytes)
                        // From Unicode 3.2, surrogate characters = illegal
                        || (($mUcs4 & 0xFFFFF800) === 0xD800)
                        // Codepoints outside the Unicode range are illegal
                        || ($mUcs4 > 0x10FFFF)
                    ) {
                        // do nothing...
                    } elseif (
                        $mUcs4 !== 0xFEFF  // omit BOM
                        // check for valid Char unicode codepoints
                        && (
                            $mUcs4 === 0x9
                            || $mUcs4 === 0xA
                            || $mUcs4 === 0xD
                            || (0x20 <= $mUcs4 && 0x7E >= $mUcs4)
                            // 7F-9F is not strictly prohibited by XML,
                            // but it is non-SGML, and thus we don't allow it
                            || (0xA0 <= $mUcs4 && 0xD7FF >= $mUcs4)
                            || (0xE000 <= $mUcs4 && 0xFFFD >= $mUcs4)
                            || (0x10000 <= $mUcs4 && 0x10FFFF >= $mUcs4)
                        )
                    ) {
                        $out .= $char;
                    }
                    // initialize UTF8 cache (reset)
                    $mState = 0;
                    $mUcs4 = 0;
                    $mBytes = 1;
                    $char = '';
                }
            } else {
                // ((0xC0 & (*in) != 0x80) && (mState != 0))
                // Incomplete multi-octet sequence.
                // used to result in complete fail, but we'll reset
                $mState = 0;
                $mUcs4 = 0;
                $mBytes = 1;
                $char = '';
            }
        }

        return $out;
    }

    /**
     * Translates a Unicode codepoint into its corresponding UTF-8 character.
     *
     * @note Based on Feyd's function at
     *       <http://forums.devnetwork.net/viewtopic.php?p=191404#191404>,
     *       which is in public domain.
     * @note While we're going to do code point parsing anyway, a good
     *       optimization would be to refuse to translate code points that
     *       are non-SGML characters.  However, this could lead to duplication.
     * @note This is very similar to the unichr function in
     *       maintenance/generate-entity-file.php (although this is superior,
     *       due to its sanity checks).
     *
     * @param int $code
     *
     * @return string
     */

    // +----------+----------+----------+----------+
    // | 33222222 | 22221111 | 111111   |          |
    // | 10987654 | 32109876 | 54321098 | 76543210 | bit
    // +----------+----------+----------+----------+
    // |          |          |          | 0xxxxxxx | 1 byte 0x00000000..0x0000007F
    // |          |          | 110yyyyy | 10xxxxxx | 2 byte 0x00000080..0x000007FF
    // |          | 1110zzzz | 10yyyyyy | 10xxxxxx | 3 byte 0x00000800..0x0000FFFF
    // | 11110www | 10wwzzzz | 10yyyyyy | 10xxxxxx | 4 byte 0x00010000..0x0010FFFF
    // +----------+----------+----------+----------+
    // | 00000000 | 00011111 | 11111111 | 11111111 | Theoretical upper limit of legal scalars: 2097151 (0x001FFFFF)
    // | 00000000 | 00010000 | 11111111 | 11111111 | Defined upper limit of legal scalar codes
    // +----------+----------+----------+----------+

    public static function unichr(int $code): string
    {
        if (
            $code > 1114111
            || $code < 0
            || ($code >= 55296 && $code <= 57343)
        ) {
            // bits are set outside the "valid" range as defined
            // by UNICODE 4.1.0
            return '';
        }

        $x = $y = $z = $w = 0;
        if ($code < 128) {
            // regular ASCII character
            $x = $code;
        } else {
            // set up bits for UTF-8
            $x = ($code & 63) | 128;
            if ($code < 2048) {
                $y = (($code & 2047) >> 6) | 192;
            } else {
                $y = (($code & 4032) >> 6) | 128;
                if ($code < 65536) {
                    $z = (($code >> 12) & 15) | 224;
                } else {
                    $z = (($code >> 12) & 63) | 128;
                    $w = (($code >> 18) & 7) | 240;
                }
            }
        }

        // set up the actual character
        $ret = '';
        if ($w) {
            $ret .= \chr($w);
        }

        if ($z) {
            $ret .= \chr($z);
        }

        if ($y) {
            $ret .= \chr($y);
        }

        $ret .= \chr($x);

        return $ret;
    }

    /**
     * @return bool
     */
    public static function iconvAvailable(): bool
    {
        if (static::$iconvAvailable === null) {
            static::$iconvAvailable = function_exists('iconv')
                                      && static::testIconvTruncateBug() !== static::ICONV_UNUSABLE;
        }

        return static::$iconvAvailable;
    }

    /**
     * Convert a string to UTF-8 based on configuration.
     *
     * @param string  $str The string to convert
     * @param Config  $config
     * @param Context $context
     *
     * @return string
     * @throws Exception
     * @throws \Exception
     */
    public static function convertToUTF8(string $str, Config $config, Context $context): string
    {
        $encoding = $config->get('Core.Encoding');
        if ($encoding === 'utf-8') {
            return $str;
        }

        static $iconv = null;
        if ($iconv === null) {
            $iconv = static::iconvAvailable();
        }

        if ($iconv && !$config->get('Test.ForceNoIconv')) {
            // unaffected by bugs, since UTF-8 support all characters
            $str = static::unsafeIconv($encoding, 'utf-8//IGNORE', $str);
            if ($str === null) {
                // $encoding is not a valid encoding
                throw new Exception("Invalid encoding {$encoding}");
            }

            // If the string is bjorked by Shift_JIS or a similar encoding
            // that doesn't support all of ASCII, convert the naughty
            // characters to their true byte-wise ASCII/UTF-8 equivalents.
            $replacements = static::testEncodingSupportsASCII($encoding);
            if (\is_array($replacements)) {
                return strtr($str, $replacements);
            }

            return $str;
        }

        if ($encoding === 'iso-8859-1') {
            return utf8_encode($str);
        }

        $bug = static::testIconvTruncateBug();
        if ($bug === static::ICONV_OK) {
            throw new Exception('Encoding not supported, please install iconv');
        }

        throw new Exception('You have a buggy version of iconv, see https://bugs.php.net/bug.php?id=48147 ' .
                             'and http://sourceware.org/bugzilla/show_bug.cgi?id=13541');
    }

    /**
     * Converts a string from UTF-8 based on configuration.
     *
     * @param string  $str The string to convert
     * @param Config  $config
     * @param ?Context $context
     *
     * @return string
     * @note Currently, this is a lossy conversion, with unexpressable
     *       characters being omitted.
     * @throws Exception
     */
    public static function convertFromUTF8(string $str, Config $config, ?Context $context): ?string
    {
        $encoding = $config->get('Core.Encoding');
        if ($escape = $config->get('Core.EscapeNonASCIICharacters')) {
            $str = static::convertToASCIIDumbLossless($str);
        }

        if ($encoding === 'utf-8') {
            return $str;
        }

        if (static::$iconvAvailable === null) {
            static::$iconvAvailable = static::iconvAvailable();
        }

        if (static::$iconvAvailable && !$config->get('Test.ForceNoIconv')) {
            // Undo our previous fix in convertToUTF8, otherwise iconv will barf
            $ascii_fix = static::testEncodingSupportsASCII($encoding);
            if (!$escape && !empty($ascii_fix) && \is_iterable($ascii_fix)) {
                $clear_fix = [];
                foreach ($ascii_fix as $utf8 => $native) {
                    $clear_fix[$utf8] = '';
                }
                $str = strtr($str, $clear_fix);
            }

            if ($ascii_fix) {
                $str = strtr($str, array_flip((array)$ascii_fix));
            }

            // Normal stuff
            return static::iconv('utf-8', $encoding . '//IGNORE', $str);
        }

        if ($encoding === 'iso-8859-1') {
            return utf8_decode($str);
        }

        throw new Exception('Encoding not supported');
        // You might be tempted to assume that the ASCII representation
        // might be OK, however, this is *not* universally true over all
        // encodings.  So we take the conservative route here, rather
        // than forcibly turn on %Core.EscapeNonASCIICharacters
    }

    /**
     * Lossless (character-wise) conversion of HTML to ASCII
     *
     * @param string $str UTF-8 string to be converted to ASCII
     *
     * @return string ASCII encoded string with non-ASCII character entity-ized
     * @warning Adapted from MediaWiki, claiming fair use: this is a common
     *       algorithm. If you disagree with this license fudgery,
     *       implement it yourself.
     * @note    Uses decimal numeric entities since they are best supported.
     * @note    This is a DUMB function: it has no concept of keeping
     *       character entities that the projected character encoding
     *       can allow. We could possibly implement a smart version
     *       but that would require it to also know which Unicode
     *       codepoints the charset supported (not an easy task).
     * @note    Sort of with cleanUTF8() but it assumes that $str is
     *       well-formed UTF-8
     */
    public static function convertToASCIIDumbLossless(string $str): string
    {
        $bytesleft = 0;
        $result = '';
        $working = 0;
        $len = strlen($str);

        for ($i = 0; $i < $len; $i++) {
            $bytevalue = ord($str[$i]);
            if ($bytevalue <= 0x7F) { //0xxx xxxx
                $result .= \chr($bytevalue);
                $bytesleft = 0;
            } elseif ($bytevalue <= 0xBF) { //10xx xxxx
                $working <<= 6;
                $working += ($bytevalue & 0x3F);
                $bytesleft--;
                if ($bytesleft <= 0) {
                    $result .= "&#${working};";
                }
            } elseif ($bytevalue <= 0xDF) { //110x xxxx
                $working = $bytevalue & 0x1F;
                $bytesleft = 1;
            } elseif ($bytevalue <= 0xEF) { //1110 xxxx
                $working = $bytevalue & 0x0F;
                $bytesleft = 2;
            } else { //1111 0xxx
                $working = $bytevalue & 0x07;
                $bytesleft = 3;
            }
        }

        return $result;
    }

    /** No bugs detected in iconv. */
    public const ICONV_OK = 0;

    /** Iconv truncates output if converting from UTF-8 to another
     *  character set with //IGNORE, and a non-encodable character is found */
    public const ICONV_TRUNCATES = 1;

    /** Iconv does not support //IGNORE, making it unusable for
     *  transcoding purposes */
    public const ICONV_UNUSABLE = 2;

    /**
     * glibc iconv has a known bug where it doesn't handle the magic
     * //IGNORE stanza correctly.  In particular, rather than ignore
     * characters, it will return an EILSEQ after consuming some number
     * of characters, and expect you to restart iconv as if it were
     * an E2BIG.  Old versions of PHP did not respect the errno, and
     * returned the fragment, so as a result you would see iconv
     * mysteriously truncating output. We can work around this by
     * manually chopping our input into segments of about 8000
     * characters, as long as PHP ignores the error code.  If PHP starts
     * paying attention to the error code, iconv becomes unusable.
     *
     * @return int Error code indicating severity of bug.
     * @throws Exception
     */
    public static function testIconvTruncateBug(): int
    {
        if (static::$iconvCode === null) {
            // better not use iconv, otherwise infinite loop!
            $r = static::unsafeIconv('utf-8', 'ascii//IGNORE', "\xCE\xB1" . str_repeat('a', 9000));

            if ($r === null) {
                return static::$iconvCode = static::ICONV_UNUSABLE;
            }

            $c = strlen($r);
            if ($c < 9000) {
                return static::$iconvCode = static::ICONV_TRUNCATES;
            }

            if ($c > 9000) {
                throw new Exception(
                    'Your copy of iconv is extremely buggy. Please notify HTML Purifier maintainers: ' .
                    'include your iconv version as per phpversion()'
                );
            } else {
                return static::$iconvCode = static::ICONV_OK;
            }
        }

        return static::$iconvCode;
    }

    /**
     * This expensive function tests whether or not a given character
     * encoding supports ASCII. 7/8-bit encodings like Shift_JIS will
     * fail this test, and require special processing. Variable width
     * encodings shouldn't ever fail.
     *
     * @param string $encoding Encoding name to test, as per iconv format
     * @param bool   $bypass   Whether or not to bypass the precompiled arrays.
     *
     * @return array|bool of UTF-8 characters to their corresponding ASCII,
     *      which can be used to "undo" any overzealous iconv action.
     */
    public static function testEncodingSupportsASCII(string $encoding, bool $bypass = false)
    {
        // All calls to iconv here are unsafe, proof by case analysis:
        // If ICONV_OK, no difference.
        // If ICONV_TRUNCATE, all calls involve one character inputs,
        // so bug is not triggered.
        // If ICONV_UNUSABLE, this call is irrelevant
        static::$asciiEncodings = [];
        if (!$bypass) {
            // fixme: this could never be true because of line 663 right?
            if (isset(static::$asciiEncodings[$encoding])) {
                return static::$asciiEncodings[$encoding];
            }

            $lenc = strtolower($encoding);
            switch ($lenc) {
                case 'shift_jis':
                    return ["\xC2\xA5" => '\\', "\xE2\x80\xBE" => '~'];
                case 'johab':
                    return ["\xE2\x82\xA9" => '\\'];
            }

            if (strncmp($lenc, 'iso-8859-', 9) === 0) {
                return [];
            }
        }

        $ret = [];
        if (static::unsafeIconv('UTF-8', $encoding, 'a') === null) {
            return false;
        }

        for ($i = 0x20; $i <= 0x7E; $i++) { // all printable ASCII chars
            $c = \chr($i); // UTF-8 char
            $r = static::unsafeIconv('UTF-8', "$encoding//IGNORE", $c); // initial conversion
            if (
                $r === ''
                // This line is needed for iconv implementations that do not
                // omit characters that do not exist in the target character set
                || ($r === $c && static::unsafeIconv($encoding, 'UTF-8//IGNORE', $r) !== $c)
            ) {
                // Reverse engineer: what's the UTF-8 equiv of this byte
                // sequence? This assumes that there's no variable width
                // encoding that doesn't support ASCII.
                $key = static::unsafeIconv($encoding, 'UTF-8//IGNORE', $c);
                if (!\is_null($key)) {
                    $ret[$key] = $c;
                }
            }
        }

        static::$asciiEncodings[$encoding] = $ret;

        return $ret;
    }
}
