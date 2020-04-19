<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef;

use \HTMLPurifier\Config;
use HTMLPurifier\Context;

/**
 * Class TestCase
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef
 */
abstract class TestCase extends \HTMLPurifier\Tests\Unit\TestCase
{
    protected $def;
    protected $context, $config;

    public function setUp(): void
    {
        $this->config = \HTMLPurifier\Config::createDefault();
        $this->context = new Context();
    }

    /**
     * cannot be used for accumulator
     *
     * @param      $string
     * @param bool $expect
     * @param bool $or_false
     */
    public function assertDef($string, $expect = true, $or_false = false)
    {
        // $expect can be a string or bool
        $result = $this->def->validate($string, $this->config, $this->context);

        if ($expect === true) {
            if (!($or_false && $result === false)) {
                static::assertEquals($string, $result);
            }
        } else if (!($or_false && $result === false)) {
            static::assertEquals($expect, $result);
        }
    }
}
