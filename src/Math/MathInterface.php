<?php

declare(strict_types=1);

namespace HTMLPurifier\Math;

/**
 * Interface MathInterface
 *
 * @package HTMLPurifier\Math
 */
interface MathInterface
{
    /**
     * Adds two numbers, using arbitrary precision when available.
     *
     * @param string $one
     * @param string $two
     * @param int    $scale
     *
     * @return string
     */
    public function add(string $one, string $two, int $scale): string;

    /**
     * Multiples two numbers, using arbitrary precision when available.
     *
     * @param string $one
     * @param string $two
     * @param int    $scale
     *
     * @return string
     */
    public function multiply(string $one, string $two, int $scale): string;

    /**
     * Divides two numbers, using arbitrary precision when available.
     *
     * @param string $one
     * @param string $two
     * @param int    $scale
     *
     * @return string
     */
    public function divide(string $one, string $two, int $scale): string;

    /**
     * Rounds a number according to the number of sigfigs it should have,
     * using arbitrary precision when available.
     *
     * @param string $one
     * @param int    $sigfigs
     *
     * @return string
     */
    public function round(string $one, int $sigfigs): string;
}
