<?php

declare(strict_types=1);

/**
 * Object that provides entity lookup table from entity name to character
 */
class HTMLPurifier_EntityLookup
{
    /**
     * Assoc array of entity name to character represented.
     * @type array
     */
    public $table;

    /**
     * Sets up the entity lookup table from the serialized file contents.
     * @param bool $file
     * @note The serialized contents are versioned, but were generated
     *       using the maintenance script generate_entity_file.php
     * @warning This is not in constructor to help enforce the Singleton
     */
    public function setup(bool $file = false)
    {
        if (!$file) {
            $file = HTMLPURIFIER_PREFIX . '/HTMLPurifier/EntityLookup/entities.ser';
        }

        $this->table = unserialize(file_get_contents($file));
    }

    /**
     * Retrieves sole instance of the object.
     * @param bool|HTMLPurifier_EntityLookup $prototype Optional prototype of custom lookup table to overload with.
     * @return HTMLPurifier_EntityLookup
     */
    public static function instance($prototype = false): HTMLPurifier_EntityLookup
    {
        // no references, since PHP doesn't copy unless modified
        static $instance = null;
        if ($prototype) {
            $instance = $prototype;
        } elseif (!$instance) {
            $instance = new static();
            $instance->setup();
        }

        return $instance;
    }
}
