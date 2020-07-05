<?php

declare(strict_types=1);

namespace HTMLPurifier\ConfigSchema;

use HTMLPurifier\ConfigSchema\Interchange\Directive;
use HTMLPurifier\ConfigSchema\Interchange\Id;

use function is_array;
use function is_bool;
use function is_string;

/**
 * Fluent interface for validating the contents of member variables.
 * This should be immutable. See HTMLPurifier_ConfigSchema_Validator for
 * use-cases. We name this an 'atom' because it's ONLY for validations that
 * are independent and usually scalar.
 */
class ValidatorAtom
{
    /**
     * @type string
     */
    protected $context;

    /**
     * @type object
     */
    protected $obj;

    /**
     * @type string
     */
    protected $member;

    /**
     * @type mixed
     */
    protected $contents;

    /**
     * HTMLPurifier_ConfigSchema_ValidatorAtom constructor.
     *
     * @param string $context
     * @param Directive|Id $obj
     * @param string $member
     */
    public function __construct(string $context, $obj, string $member)
    {
        $this->context = $context;
        $this->obj = $obj;
        $this->member = $member;
        $this->contents =& $obj->$member;
    }

    /**
     * @return ValidatorAtom
     * @throws Exception
     */
    public function assertIsString(): ValidatorAtom
    {
        if (!is_string($this->contents)) {
            $this->error('must be a string');
        }

        return $this;
    }

    /**
     * @return ValidatorAtom
     * @throws Exception
     */
    public function assertIsBool(): ValidatorAtom
    {
        if (!is_bool($this->contents)) {
            $this->error('must be a boolean');
        }

        return $this;
    }

    /**
     * @return ValidatorAtom
     * @throws Exception
     */
    public function assertIsArray(): ValidatorAtom
    {
        if (!is_array($this->contents)) {
            $this->error('must be an array');
        }

        return $this;
    }

    /**
     * @return ValidatorAtom
     * @throws Exception
     */
    public function assertNotNull(): ValidatorAtom
    {
        if ($this->contents === null) {
            $this->error('must not be null');
        }

        return $this;
    }

    /**
     * @return ValidatorAtom
     * @throws Exception
     */
    public function assertAlnum(): ValidatorAtom
    {
        $this->assertIsString();
        if (!ctype_alnum($this->contents)) {
            $this->error('must be alphanumeric');
        }

        return $this;
    }

    /**
     * @return ValidatorAtom
     * @throws Exception
     */
    public function assertNotEmpty(): ValidatorAtom
    {
        if (empty($this->contents)) {
            $this->error('must not be empty');
        }

        return $this;
    }

    /**
     * @return ValidatorAtom
     * @throws Exception
     */
    public function assertIsLookup(): ValidatorAtom
    {
        $this->assertIsArray();
        foreach ($this->contents as $v) {
            if ($v !== true) {
                $this->error('must be a lookup array');
            }
        }

        return $this;
    }

    /**
     * @param string $msg
     *
     * @throws Exception
     */
    protected function error(string $msg): void
    {
        throw new Exception(ucfirst($this->member) . ' in ' . $this->context . ' ' . $msg);
    }
}
