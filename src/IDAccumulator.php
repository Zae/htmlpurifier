<?php

declare(strict_types=1);

namespace HTMLPurifier;

use \HTMLPurifier\Config;
use HTMLPurifier\Exception;

/**
 * Component of HTMLPurifier_AttrContext that accumulates IDs to prevent dupes
 *
 * @note In Slashdot-speak, dupe means duplicate.
 * @note The default constructor does not accept $config or $context objects:
 *       use must use the static build() factory method to perform initialization.
 */
class IDAccumulator
{
    /**
     * Lookup table of IDs we've accumulated.
     *
     * @public
     */
    public $ids = [];

    /**
     * Builds an IDAccumulator, also initializing the default blacklist
     *
     * @param Config  $config  Instance of \HTMLPurifier\Config
     * @param Context $context Instance of HTMLPurifier\HTMLPurifier_Context
     *
     * @return IDAccumulator Fully initialized HTMLPurifier\HTMLPurifier_IDAccumulator
     * @throws Exception
     */
    public static function build(\HTMLPurifier\Config $config, $context): IDAccumulator
    {
        $id_accumulator = new static();
        $id_accumulator->load($config->get('Attr.IDBlacklist'));

        return $id_accumulator;
    }

    /**
     * Add an ID to the lookup table.
     *
     * @param string $id ID to be added.
     *
     * @return bool status, true if success, false if there's a dupe
     */
    public function add(string $id): bool
    {
        if (isset($this->ids[$id])) {
            return false;
        }

        return $this->ids[$id] = true;
    }

    /**
     * Load a list of IDs into the lookup table
     *
     * @param array $array_of_ids of IDs to load
     *
     * @note This function doesn't care about duplicates
     */
    public function load(array $array_of_ids): void
    {
        foreach ($array_of_ids as $id) {
            $this->ids[$id] = true;
        }
    }
}
