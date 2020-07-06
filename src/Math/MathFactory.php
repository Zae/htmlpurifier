<?php

declare(strict_types=1);

namespace HTMLPurifier\Math;

use function extension_loaded;

/**
 * Class MathFactory
 *
 * @package HTMLPurifier\Math
 */
class MathFactory
{
    /**
     * Return instance of MathInterface that works according to loaded
     * extensions and preference given to the function.
     *
     * @return MathInterface
     */
    public static function make(bool $force_native = false): MathInterface
    {
        switch (true) {
            case (!$force_native && extension_loaded('bcmath')):
                return new BCMath();
            default:
                return new Native();
        }
    }
}
