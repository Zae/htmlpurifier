<?php

declare(strict_types=1);

/**
 * Represents a directive ID in the interchange format.
 */
class HTMLPurifier_ConfigSchema_Interchange_Id
{
    /**
     * @type string
     */
    public $key;

    /**
     * @param string $key
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     * @warning This is NOT magic, to ensure that people don't abuse SPL and
     *          cause problems for PHP 5.0 support.
     */
    public function toString()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getRootNamespace(): string
    {
        return substr($this->key, 0, strpos($this->key, '.'));
    }

    /**
     * @return string
     */
    public function getDirective(): string
    {
        return substr($this->key, strpos($this->key, '.') + 1);
    }

    /**
     * @param string $id
     *
     * @return HTMLPurifier_ConfigSchema_Interchange_Id
     */
    public static function make(string $id): HTMLPurifier_ConfigSchema_Interchange_Id
    {
        return new HTMLPurifier_ConfigSchema_Interchange_Id($id);
    }
}
