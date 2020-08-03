<?php

declare(strict_types=1);

namespace HTMLPurifier\ChildDef;

use HTMLPurifier\Context;
use HTMLPurifier\ChildDef;
use HTMLPurifier\Node\Element;
use HTMLPurifier\Node\Text;
use HTMLPurifier\Config;

use function count;

/**
 * Definition that allows a set of elements, but disallows empty children.
 */
class Required extends ChildDef
{
    /**
     * Lookup table of allowed elements.
     *
     * @var array
     */
    public $elements = [];

    /**
     * Whether or not the last passed node was all whitespace.
     *
     * @var bool
     */
    protected $whitespace = false;

    /**
     * @param array|string $elements List of allowed element names (lowercase).
     */
    public function __construct($elements)
    {
        if (\is_string($elements)) {
            $elements = str_replace(' ', '', $elements);
            $elements = explode('|', $elements);
        }

        $keys = array_keys($elements);

        if ($keys === array_keys($keys)) {
            $elements = array_flip($elements);
            foreach ($elements as $i => $x) {
                $elements[$i] = true;
                if (empty($i)) {
                    unset($elements[$i]);
                } // remove blank
            }
        }

        $this->elements = $elements;
    }

    /**
     * @var bool
     */
    public $allow_empty = false;

    /**
     * @var string
     */
    public $type = 'required';

    /**
     * @param array   $children
     * @param Config  $config
     * @param Context $context
     *
     * @return array|bool
     */
    public function validateChildren(array $children, Config $config, Context $context)
    {
        // Flag for subclasses
        $this->whitespace = false;

        // if there are no tokens, delete parent node
        if (empty($children)) {
            return false;
        }

        // the new set of children
        $result = [];

        // whether or not parsed character data is allowed
        // this controls whether or not we silently drop a tag
        // or generate escaped HTML from it
        $pcdata_allowed = isset($this->elements['#PCDATA']);

        // a little sanity check to make sure it's not ALL whitespace
        $all_whitespace = true;

        $stack = array_reverse($children);
        while (!empty($stack)) {
            $node = array_pop($stack);
            if (!empty($node->is_whitespace)) {
                $result[] = $node;
                continue;
            }
            $all_whitespace = false; // phew, we're not talking about whitespace

            if (!isset($this->elements[$node->name])) {
                // special case text
                // XXX One of these ought to be redundant or something
                if ($pcdata_allowed && $node instanceof Text) {
                    $result[] = $node;
                    continue;
                }

                // spill the child contents in
                // ToDo: Make configurable
                if ($node instanceof Element) {
                    for ($i = count($node->children) - 1; $i >= 0; $i--) {
                        $stack[] = $node->children[$i];
                    }

                    continue;
                }

                continue;
            }

            $result[] = $node;
        }
        if (empty($result)) {
            return false;
        }

        if ($all_whitespace) {
            $this->whitespace = true;
            return false;
        }

        return $result;
    }
}
