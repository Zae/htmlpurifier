<?php

declare(strict_types=1);

namespace HTMLPurifier\ConfigSchema\Interchange;

/**
 * Represents a directive ID in the interchange format.
 */
class Id
{
    /**
     * @var string
     */
    public $key;

    /**
     * @param string $key
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     * @warning This is NOT magic, to ensure that people don't abuse SPL and
     *          cause problems for PHP 5.0 support.
     */
    public function toString(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getRootNamespace(): string
    {
        return substr($this->key, 0, (int)strpos($this->key, '.'));
    }

    /**
     * @return string
     */
    public function getDirective(): string
    {
        return substr($this->key, (int)strpos($this->key, '.') + 1);
    }

    /**
     * @param string $id
     *
     * @return Id
     */
    public static function make(string $id): Id
    {
        return new Id($id);
    }
}
