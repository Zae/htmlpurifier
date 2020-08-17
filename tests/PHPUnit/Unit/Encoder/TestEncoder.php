<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Encoder;

use HTMLPurifier\Encoder;

/**
 * Special version of Encoder that can change the value of the cached
 * static iconvCode variable at will. This way we can 'emulate' the bug.
 *
 * Class TestEncoder
 *
 * @package HTMLPurifier\Tests\Unit
 */
class TestEncoder extends Encoder
{
    /**
     * Change the static iconvCode in the class.
     *
     * @param callable $callback
     * @param int      $code
     */
    public static function enableIconvBug(callable $callback, int $code = self::ICONV_TRUNCATES): void
    {
        $backup = static::$iconvCode;
        static::$iconvCode = $code;

        $callback();

        static::$iconvCode = $backup;
    }
}
