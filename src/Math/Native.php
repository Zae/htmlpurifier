<?php

declare(strict_types=1);

namespace HTMLPurifier\Math;

use function strlen;

/**
 * Class Native
 *
 * @package HTMLPurifier\Math
 */
class Native implements MathInterface
{
    /**
     * Adds two numbers, using arbitrary precision when available.
     *
     *
     * @param string $one
     * @param string $two
     * @param int    $scale
     *
     * @return string
     */
    public function add(string $one, string $two, int $scale): string
    {
        return $this->scale((float)$one + (float)$two, $scale);
    }

    /**
     * Multiples two numbers, using arbitrary precision when available.
     *
     * @param string $one
     * @param string $two
     * @param int    $scale
     *
     * @return string
     */
    public function multiply(string $one, string $two, int $scale): string
    {
        return $this->scale((float)$one * (float)$two, $scale);
    }

    /**
     * Divides two numbers, using arbitrary precision when available.
     *
     * @param string $one
     * @param string $two
     * @param int    $scale
     *
     * @return string
     */
    public function divide(string $one, string $two, int $scale): string
    {
        return $this->scale((float)$one / (float)$two, $scale);
    }

    /**
     * Rounds a number according to the number of sigfigs it should have,
     * using arbitrary precision when available.
     *
     * @param string $one
     * @param int    $sigfigs
     *
     * @return string
     */
    public function round(string $one, int $sigfigs): string
    {
        $new_log = (int)floor(log(abs((float)$one), 10)); // Number of digits left of decimal - 1
        $rp = $sigfigs - $new_log - 1; // Number of decimal places needed

        return $this->scale(round((float)$one, $sigfigs - $new_log - 1), $rp + 1);
    }

    /**
     * Scales a float to $scale digits right of decimal point, like BCMath.
     *
     * @param float $r
     * @param int   $scale
     *
     * @return string
     */
    private function scale(float $r, int $scale): string
    {
        if ($scale < 0) {
            // The f sprintf type doesn't support negative numbers, so we
            // need to cludge things manually. First get the string.
            $r = sprintf('%.0f', $r);
            // Due to floating point precision loss, $r will more than likely
            // look something like 4652999999999.9234. We grab one more digit
            // than we need to precise from $r and then use that to round
            // appropriately.
            $precise = (string)round((float)substr($r, 0, strlen($r) + $scale), -1);

            // Now we return it, truncating the zero that was rounded off.
            return substr($precise, 0, -1) . str_repeat('0', -$scale + 1);
        }

        return sprintf('%.' . $scale . 'f', $r);
    }
}
