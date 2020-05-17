<?php

declare(strict_types=1);

namespace HTMLPurifier;

/**
 * Abstract class representing Definition cache managers that implements
 * useful common methods and is a factory.
 *
 * @todo Create a separate maintenance file advanced users can use to
 *       cache their custom HTMLDefinition, which can be loaded
 *       via a configuration directive
 * @todo Implement memcached
 */
abstract class DefinitionCache
{
    /**
     * @type string|null
     */
    public $type;

    /**
     * @param string $type Type of definition objects this instance of the
     *                     cache will handle.
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * Generates a unique identifier for a particular configuration
     *
     * @param Config $config Instance of \HTMLPurifier\Config
     *
     * @return string
     * @throws Exception
     */
    public function generateKey(Config $config): string
    {
        return $config->version . ',' . // possibly replace with function calls
               $config->getBatchSerial($this->type) . ',' .
               $config->get($this->type . '.DefinitionRev');
    }

    /**
     * Tests whether or not a key is old with respect to the configuration's
     * version and revision number.
     *
     * @param string $key    Key to test
     * @param Config $config Instance of \HTMLPurifier\Config to test against
     *
     * @return bool
     * @throws Exception
     */
    public function isOld(string $key, Config $config): bool
    {
        if (substr_count($key, ',') < 2) {
            return true;
        }

        [$version, $hash, $revision] = explode(',', $key, 3);
        $compare = version_compare($version, $config->version);

        // version mismatch, is always old
        if ($compare !== 0) {
            return true;
        }

        // versions match, ids match, check revision number
        if (
            $hash === $config->getBatchSerial($this->type) &&
            $revision < $config->get($this->type . '.DefinitionRev')
        ) {
            return true;
        }

        return false;
    }

    /**
     * Checks if a definition's type jives with the cache's type
     *
     * @note Throws an error on failure
     *
     * @param Definition $def Definition object to check
     *
     * @return bool true if good, false if not
     */
    public function checkDefType(Definition $def): bool
    {
        if ($def->type !== $this->type) {
            trigger_error("Cannot use definition of type {$def->type} in cache for {$this->type}");

            return false;
        }

        return true;
    }

    /**
     * Adds a definition object to the cache
     *
     * @param Definition $def
     * @param Config     $config
     * @return mixed
     */
    abstract public function add(Definition $def, Config $config);

    /**
     * Unconditionally saves a definition object to the cache
     *
     * @param Definition $def
     * @param Config     $config
     * @return mixed
     */
    abstract public function set(Definition $def, Config $config);

    /**
     * Replace an object in the cache
     *
     * @param Definition $def
     * @param Config     $config
     * @return mixed
     */
    abstract public function replace(Definition $def, Config $config);

    /**
     * Retrieves a definition object from the cache
     *
     * @param Config $config
     * @return mixed
     */
    abstract public function get(Config $config);

    /**
     * Removes a definition object to the cache
     *
     * @param Config $config
     * @return bool
     */
    abstract public function remove(Config $config): bool;

    /**
     * Clears all objects from cache
     *
     * @param Config $config
     * @return bool
     */
    abstract public function flush(Config $config): bool;

    /**
     * Clears all expired (older version or revision) objects from cache
     *
     * @note Be careful implementing this method as flush. Flush must
     *       not interfere with other Definition types, and cleanup()
     *       should not be repeatedly called by userland code.
     *
     * @param Config $config
     * @return bool
     */
    abstract public function cleanup(Config $config): bool;
}
