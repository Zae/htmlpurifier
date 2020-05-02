<?php

declare(strict_types=1);

namespace HTMLPurifier;

use HTMLPurifier\AttrDef\CSS\Number;

/**
 * Represents a measurable length, with a string numeric magnitude
 * and a unit. This object is immutable.
 */
class Length
{
    /**
     * String numeric magnitude.
     *
     * @var string
     */
    protected $n;

    /**
     * String unit. False is permitted if $n = 0.
     *
     * @var string|bool
     */
    protected $unit;

    /**
     * Whether or not this length is valid. Null if not calculated yet.
     *
     * @var bool|null
     */
    protected $isValid;

    /**
     * Array Lookup array of units recognized by CSS 3
     *
     * @var array
     */
    protected static $allowedUnits = [
        'em' => true, 'ex' => true, 'px' => true, 'in' => true,
        'cm' => true, 'mm' => true, 'pt' => true, 'pc' => true,
        'ch' => true, 'rem' => true, 'vw' => true, 'vh' => true,
        'vmin' => true, 'vmax' => true
    ];

    /**
     * @param string      $n Magnitude
     * @param bool|string $u Unit
     */
    public function __construct(string $n = '0', $u = false)
    {
        $this->n = $n;
        $this->unit = $u !== false ? (string)$u : false;
    }

    /**
     * @param string|self $s Unit string, like '2em' or '3.4in'
     *
     * @return Length
     * @warning Does not perform validation.
     *
     * @psalm-suppress PossiblyInvalidArgument psalm doesn't understand the return with instanceof static.
     */
    public static function make($s): self
    {
        if ($s instanceof static) {
            return $s;
        }

        $n_length = strspn($s, '1234567890.+-');
        $n = substr($s, 0, $n_length);
        $unit = substr($s, $n_length);

        if ($unit === '') {
            $unit = false;
        }

        return new static($n, $unit);
    }

    /**
     * Validates the number and unit.
     *
     * @return bool
     */
    protected function validate(): bool
    {
        // Special case:
        if ($this->n === '+0' || $this->n === '-0') {
            $this->n = '0';
        }

        if ($this->n === '0' && $this->unit === false) {
            return true;
        }

        if (!ctype_lower((string)$this->unit)) {
            $this->unit = strtolower((string)$this->unit);
        }

        if (!isset(static::$allowedUnits[$this->unit])) {
            return false;
        }

        // Hack:
        $def = new Number();
        $result = $def->validate($this->n, null, null);
        if ($result === false) {
            return false;
        }

        $this->n = $result;

        return true;
    }

    /**
     * Returns string representation of number.
     *
     * @return string|bool
     */
    public function toString()
    {
        if (!$this->isValid()) {
            return false;
        }

        return $this->n . $this->unit;
    }

    /**
     * Retrieves string numeric magnitude.
     *
     * @return string
     */
    public function getN(): string
    {
        return $this->n;
    }

    /**
     * Retrieves string unit.
     *
     * @return string|bool
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Returns true if this length unit is valid.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        if ($this->isValid === null) {
            $this->isValid = $this->validate();
        }

        return $this->isValid;
    }

    /**
     * Compares two lengths, and returns 1 if greater, -1 if less and 0 if equal.
     *
     * @param Length $l
     *
     * @return int|bool
     * @warning If both values are too large or small, this calculation will
     *          not work properly
     */
    public function compareTo(Length $l)
    {
        if ($l->unit !== $this->unit) {
            $converter = new UnitConverter();
            $l = $converter->convert($l, $this->unit);

            if (!$l instanceof self) {
                return false;
            }
        }

        return (int)$this->n - (int)$l->n;
    }
}
