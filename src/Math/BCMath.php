<?php

declare(strict_types=1);

namespace HTMLPurifier\Math;

use function is_null;
use function strlen;

/**
 * Class BCMath
 *
 * @package HTMLPurifier\Math
 */
class BCMath implements MathInterface
{
    /**
     * Adds two numbers, using arbitrary precision when available.
     *
     * @param string $one
     * @param string $two
     * @param int    $scale
     *
     * @return string
     *
     * @psalm-suppress ArgumentTypeCoercion
     */
    public function add(string $one, string $two, int $scale): string
    {
        return bcadd($one, $two, $scale);
    }

    /**
     * Multiples two numbers, using arbitrary precision when available.
     *
     * @param string $one
     * @param string $two
     * @param int    $scale
     *
     * @return string
     *
     * @psalm-suppress ArgumentTypeCoercion
     */
    public function multiply(string $one, string $two, int $scale): string
    {
        return bcmul($one, $two, $scale);
    }

    /**
     * Divides two numbers, using arbitrary precision when available.
     *
     * @param string $one
     * @param string $two
     * @param int    $scale
     *
     * @return string
     *
     * @psalm-suppress ArgumentTypeCoercion
     */
    public function divide(string $one, string $two, int $scale): string
    {
        $out = bcdiv($one, $two, $scale);

        if (is_null($out)) {
            return '0';
        }

        return $out;
    }

    /**
     * Rounds a number according to the number of sigfigs it should have,
     * using arbitrary precision when available.
     *
     * @param string $n
     * @param int    $sigfigs
     *
     * @return string
     *
     * @psalm-suppress ArgumentTypeCoercion
     */
    public function round(string $n, int $sigfigs): string
    {
        $new_log = (int)floor(log(abs((float)$n), 10)); // Number of digits left of decimal - 1
        $rp = $sigfigs - $new_log - 1; // Number of decimal places needed
        $neg = $n < 0 ? '-' : ''; // Negative sign

        if ($rp >= 0) {
            $out = bcadd($n, $neg . '0.' . str_repeat('0', $rp) . '5', $rp + 1);
            $out = bcdiv($out, '1', $rp);
        } else {
            // This algorithm partially depends on the standardized
            // form of numbers that comes out of bcmath.
            $out = bcadd($n, $neg . '5' . str_repeat('0', $new_log - $sigfigs), 0);
            $out = substr($out, 0, $sigfigs + strlen($neg)) . str_repeat('0', $new_log - $sigfigs + 1);
        }

        /**
         * @psalm-suppress DocblockTypeContradiction
         */
        if (is_null($out)) {
            return '0';
        }

        return $out;
    }
}
