<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrTransform;

use HTMLPurifier\AttrDef\HTML\ID;
use HTMLPurifier\Context;
use HTMLPurifier\AttrTransform;
use HTMLPurifier_Config;
use HTMLPurifier_Exception;

/**
 * Post-transform that performs validation to the name attribute; if
 * it is present with an equivalent id attribute, it is passed through;
 * otherwise validation is performed.
 */
class NameSync extends AttrTransform
{
    /**
     * @var ID
     */
    private $idDef;

    public function __construct()
    {
        $this->idDef = new ID();
    }

    /**
     * @param array               $attr
     * @param HTMLPurifier_Config $config
     * @param Context             $context
     *
     * @return array
     * @throws HTMLPurifier_Exception
     */
    public function transform(array $attr, HTMLPurifier_Config $config, Context $context): array
    {
        if (!isset($attr['name'])) {
            return $attr;
        }

        $name = $attr['name'];
        if (isset($attr['id']) && $attr['id'] === $name) {
            return $attr;
        }

        $result = $this->idDef->validate($name, $config, $context);
        if ($result === false) {
            unset($attr['name']);
        } else {
            $attr['name'] = $result;
        }

        return $attr;
    }
}
