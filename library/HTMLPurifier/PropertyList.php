<?php

declare(strict_types=1);

/**
 * Generic property list implementation
 */
class HTMLPurifier_PropertyList
{
    /**
     * Internal data-structure for properties.
     *
     * @type array
     */
    protected $data = [];

    /**
     * Parent plist.
     *
     * @type HTMLPurifier_PropertyList
     */
    protected $parent;

    /**
     * Cache.
     *
     * @type array
     */
    protected $cache;

    /**
     * @param HTMLPurifier_PropertyList $parent Parent plist
     */
    public function __construct($parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * Recursively retrieves the value for a key
     *
     * @param string $name
     *
     * @return mixed
     * @throws HTMLPurifier_Exception
     */
    public function get(string $name)
    {
        if ($this->has($name)) {
            return $this->data[$name];
        }

        // possible performance bottleneck, convert to iterative if necessary
        if ($this->parent) {
            return $this->parent->get($name);
        }

        throw new HTMLPurifier_Exception("Key '{$name}' not found");
    }

    /**
     * Sets the value of a key, for this plist
     *
     * @param string $name
     * @param mixed  $value
     */
    public function set(string $name, $value): void
    {
        $this->data[$name] = $value;
    }

    /**
     * Returns true if a given key exists
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name): bool
    {
        return array_key_exists($name, $this->data);
    }

    /**
     * Resets a value to the value of it's parent, usually the default. If
     * no value is specified, the entire plist is reset.
     *
     * @param string $name
     */
    public function reset(?string $name = null): void
    {
        if ($name === null) {
            $this->data = [];
        } else {
            unset($this->data[$name]);
        }
    }

    /**
     * Squashes this property list and all of its property lists into a single
     * array, and returns the array. This value is cached by default.
     *
     * @param bool $force If true, ignores the cache and regenerates the array.
     *
     * @return array
     */
    public function squash(bool $force = false): ?array
    {
        if ($this->cache !== null && !$force) {
            return $this->cache;
        }

        if ($this->parent) {
            return $this->cache = array_merge($this->parent->squash($force), $this->data);
        }

        return $this->cache = $this->data;
    }

    /**
     * Returns the parent plist.
     *
     * @return HTMLPurifier_PropertyList
     */
    public function getParent(): ?HTMLPurifier_PropertyList
    {
        return $this->parent;
    }

    /**
     * Sets the parent plist.
     *
     * @param HTMLPurifier_PropertyList $plist Parent plist
     */
    public function setParent(HTMLPurifier_PropertyList $plist): void
    {
        $this->parent = $plist;
    }
}
