<?php

declare(strict_types=1);

use HTMLPurifier\ConfigSchema\Exception;

/**
 * Fluent interface for validating the contents of member variables.
 * This should be immutable. See HTMLPurifier_ConfigSchema_Validator for
 * use-cases. We name this an 'atom' because it's ONLY for validations that
 * are independent and usually scalar.
 */
class HTMLPurifier_ConfigSchema_ValidatorAtom
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
     * @param        $obj
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
     * @return HTMLPurifier_ConfigSchema_ValidatorAtom
     * @throws Exception
     */
    public function assertIsString(): HTMLPurifier_ConfigSchema_ValidatorAtom
    {
        if (!is_string($this->contents)) {
            $this->error('must be a string');
        }

        return $this;
    }

    /**
     * @return HTMLPurifier_ConfigSchema_ValidatorAtom
     * @throws Exception
     */
    public function assertIsBool(): HTMLPurifier_ConfigSchema_ValidatorAtom
    {
        if (!is_bool($this->contents)) {
            $this->error('must be a boolean');
        }

        return $this;
    }

    /**
     * @return HTMLPurifier_ConfigSchema_ValidatorAtom
     * @throws Exception
     */
    public function assertIsArray(): HTMLPurifier_ConfigSchema_ValidatorAtom
    {
        if (!is_array($this->contents)) {
            $this->error('must be an array');
        }

        return $this;
    }

    /**
     * @return HTMLPurifier_ConfigSchema_ValidatorAtom
     * @throws Exception
     */
    public function assertNotNull(): HTMLPurifier_ConfigSchema_ValidatorAtom
    {
        if ($this->contents === null) {
            $this->error('must not be null');
        }

        return $this;
    }

    /**
     * @return HTMLPurifier_ConfigSchema_ValidatorAtom
     * @throws Exception
     */
    public function assertAlnum(): HTMLPurifier_ConfigSchema_ValidatorAtom
    {
        $this->assertIsString();
        if (!ctype_alnum($this->contents)) {
            $this->error('must be alphanumeric');
        }

        return $this;
    }

    /**
     * @return HTMLPurifier_ConfigSchema_ValidatorAtom
     * @throws Exception
     */
    public function assertNotEmpty(): HTMLPurifier_ConfigSchema_ValidatorAtom
    {
        if (empty($this->contents)) {
            $this->error('must not be empty');
        }

        return $this;
    }

    /**
     * @return HTMLPurifier_ConfigSchema_ValidatorAtom
     * @throws Exception
     */
    public function assertIsLookup(): HTMLPurifier_ConfigSchema_ValidatorAtom
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
