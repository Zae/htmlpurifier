<?php

declare(strict_types=1);

use HTMLPurifier\Definition;

/**
 * Abstract class representing Definition cache managers that implements
 * useful common methods and is a factory.
 *
 * @todo Create a separate maintenance file advanced users can use to
 *       cache their custom HTMLDefinition, which can be loaded
 *       via a configuration directive
 * @todo Implement memcached
 */
abstract class HTMLPurifier_DefinitionCache
{
    /**
     * @type string
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
     * @param HTMLPurifier_Config $config Instance of HTMLPurifier_Config
     *
     * @return string
     * @throws HTMLPurifier_Exception
     */
    public function generateKey(HTMLPurifier_Config $config)
    {
        return $config->version . ',' . // possibly replace with function calls
               $config->getBatchSerial($this->type) . ',' .
               $config->get($this->type . '.DefinitionRev');
    }

    /**
     * Tests whether or not a key is old with respect to the configuration's
     * version and revision number.
     *
     * @param string              $key    Key to test
     * @param HTMLPurifier_Config $config Instance of HTMLPurifier_Config to test against
     *
     * @return bool
     * @throws HTMLPurifier_Exception
     */
    public function isOld(string $key, HTMLPurifier_Config $config)
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
        if ($hash === $config->getBatchSerial($this->type) &&
            $revision < $config->get($this->type . '.DefinitionRev')) {
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
    public function checkDefType(Definition $def)
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
     * @param Definition          $def
     * @param HTMLPurifier_Config $config
     */
    abstract public function add(Definition $def, HTMLPurifier_Config $config);

    /**
     * Unconditionally saves a definition object to the cache
     *
     * @param Definition          $def
     * @param HTMLPurifier_Config $config
     */
    abstract public function set(Definition $def, HTMLPurifier_Config $config);

    /**
     * Replace an object in the cache
     *
     * @param Definition          $def
     * @param HTMLPurifier_Config $config
     */
    abstract public function replace(Definition $def, HTMLPurifier_Config $config);

    /**
     * Retrieves a definition object from the cache
     *
     * @param HTMLPurifier_Config $config
     */
    abstract public function get(HTMLPurifier_Config $config);

    /**
     * Removes a definition object to the cache
     *
     * @param HTMLPurifier_Config $config
     */
    abstract public function remove(HTMLPurifier_Config $config);

    /**
     * Clears all objects from cache
     *
     * @param HTMLPurifier_Config $config
     */
    abstract public function flush(HTMLPurifier_Config $config);

    /**
     * Clears all expired (older version or revision) objects from cache
     *
     * @note Be careful implementing this method as flush. Flush must
     *       not interfere with other Definition types, and cleanup()
     *       should not be repeatedly called by userland code.
     *
     * @param HTMLPurifier_Config $config
     */
    abstract public function cleanup(HTMLPurifier_Config $config);
}
