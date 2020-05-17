<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\EntityLookup;
use HTMLPurifier\EntityParser;

/**
 * Class EntityParserTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class EntityParserTest extends TestCase
{
    private $EntityParser;
    private $_entity_lookup;

    public function setUp(): void
    {
        $this->EntityParser = new EntityParser();
        $this->_entity_lookup = EntityLookup::instance();
    }

    /**
     * @test
     */
    public function test_substituteNonSpecialEntities(): void
    {
        $char_theta = $this->_entity_lookup->table['theta'];
        static::assertEquals(
            $char_theta,
            $this->EntityParser->substituteNonSpecialEntities('&theta;')
        );
        static::assertEquals(
            $char_theta,
            $this->EntityParser->substituteTextEntities('&theta;')
        );
        static::assertEquals(
            '"',
            $this->EntityParser->substituteNonSpecialEntities('"')
        );
        static::assertEquals(
            '"',
            $this->EntityParser->substituteTextEntities('"')
        );

        // numeric tests, adapted from Feyd
        $args = [];
        $args[] = [1114112,false];
        $args[] = [1114111,'F48FBFBF']; // 0x0010FFFF
        $args[] = [1048576,'F4808080']; // 0x00100000
        $args[] = [1048575,'F3BFBFBF']; // 0x000FFFFF
        $args[] = [262144, 'F1808080']; // 0x00040000
        $args[] = [262143, 'F0BFBFBF']; // 0x0003FFFF
        $args[] = [65536,  'F0908080']; // 0x00010000
        $args[] = [65535,  'EFBFBF']; // 0x0000FFFF
        $args[] = [57344,  'EE8080']; // 0x0000E000
        $args[] = [57343,  false]; // 0x0000DFFF  these are ill-formed
        $args[] = [56040,  false]; // 0x0000DAE8  these are ill-formed
        $args[] = [55296,  false]; // 0x0000D800  these are ill-formed
        $args[] = [55295,  'ED9FBF']; // 0x0000D7FF
        $args[] = [53248,  'ED8080']; // 0x0000D000
        $args[] = [53247,  'ECBFBF']; // 0x0000CFFF
        $args[] = [4096,   'E18080']; // 0x00001000
        $args[] = [4095,   'E0BFBF']; // 0x00000FFF
        $args[] = [2048,   'E0A080']; // 0x00000800
        $args[] = [2047,   'DFBF']; // 0x000007FF
        $args[] = [128,    'C280']; // 0x00000080  invalid SGML char
        $args[] = [127,    '7F']; // 0x0000007F  invalid SGML char
        $args[] = [0,      '00']; // 0x00000000  invalid SGML char

        $args[] = [20108,  'E4BA8C']; // 0x00004E8C
        $args[] = [77,     '4D']; // 0x0000004D
        $args[] = [66306,  'F0908C82']; // 0x00010302
        $args[] = [1072,   'D0B0']; // 0x00000430

        foreach ($args as $arg) {
            $string = '&#' . $arg[0] . ';' . // decimal
                      '&#x' . dechex($arg[0]) . ';'; // hex
            $expect = '';
            if ($arg[1] !== false) {
                // this is only for PHP 5, the below is PHP 5 and PHP 4
                //$chars = str_split($arg[1], 2);
                $chars = [];
                // strlen must be called in loop because strings size changes
                for ($i = 0; \strlen($arg[1]) > $i; $i += 2) {
                    $chars[] = $arg[1][$i] . $arg[1][$i+1];
                }
                foreach ($chars as $char) {
                    $expect .= \chr(hexdec($char));
                }
                $expect .= $expect; // double it
            }

            static::assertEquals(
                $expect,
                $this->EntityParser->substituteNonSpecialEntities($string),
                'Identical expectation [Hex: '. dechex($arg[0]) .']'
            );

            static::assertEquals(
                $expect,
                $this->EntityParser->substituteTextEntities($string),
                'Identical expectation [Hex: '. dechex($arg[0]) .']'
            );
        }
    }

    /**
     * @test
     */
    public function test_substituteSpecialEntities(): void
    {
        static::assertEquals(
            "'",
            $this->EntityParser->substituteSpecialEntities('&#39;')
        );

        static::assertEquals(
            "'",
            $this->EntityParser->substituteTextEntities('&#39;')
        );
    }
}
