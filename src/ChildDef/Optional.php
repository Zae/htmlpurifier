<?php

declare(strict_types=1);

namespace HTMLPurifier\ChildDef;

use HTMLPurifier\Context;
use HTMLPurifier\Config;

/**
 * Definition that allows a set of elements, and allows no children.
 *
 * @note This is a hack to reuse code from HTMLPurifier\ChildDef\HTMLPurifier_ChildDef_Required,
 *       really, one shouldn't inherit from the other.  Only altered behavior
 *       is to overload a returned false with an array.  Thus, it will never
 *       return false.
 */
class Optional extends Required
{
    /**
     * @type bool
     */
    public $allow_empty = true;

    /**
     * @type string
     */
    public $type = 'optional';

    /**
     * @param array   $children
     * @param Config  $config
     * @param Context $context
     *
     * @return array|bool
     */
    public function validateChildren(array $children, Config $config, Context $context)
    {
        $result = parent::validateChildren($children, $config, $context);
        // we assume that $children is not modified
        if ($result === false) {
            if (empty($children)) {
                return true;
            }

            if ($this->whitespace) {
                return $children;
            }

            return [];
        }

        return $result;
    }
}
