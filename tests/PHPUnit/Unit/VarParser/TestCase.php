<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\VarParser;

use HTMLPurifier\VarParserException;

/**
 * Class TestCase
 *
 * @package HTMLPurifier\Tests\Unit\VarParser
 */
abstract class TestCase extends \HTMLPurifier\Tests\Unit\TestCase
{
    protected $parser;

    /**
     * @param      $var
     * @param      $type
     * @param null $ret
     */
    public function assertValid($var, $type, $ret = null): void
    {
        $ret = $ret ?? $var;
        static::assertEquals($ret, $this->parser->parse($var, $type));
    }

    /**
     * @param      $var
     * @param      $type
     * @param null $msg
     */
    public function assertInvalid($var, $type, $msg = null): void
    {
        $caught = false;
        try {
            $this->parser->parse($var, $type);
        } catch (VarParserException $e) {
            $caught = true;
            if ($msg !== null) {
                static::assertEquals($msg, $e->getMessage());
            }
        }

        if (!$caught) {
            static::fail('Did not catch expected error');
        }
    }
}
