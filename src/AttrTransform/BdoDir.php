<?php

declare(strict_types=1);

// this MUST be placed in post, as it assumes that any value in dir is valid
namespace HTMLPurifier\AttrTransform;

use HTMLPurifier\Context;
use HTMLPurifier\AttrTransform;
use \HTMLPurifier\Config;
use HTMLPurifier\Exception;

/**
 * Post-trasnform that ensures that bdo tags have the dir attribute set.
 */
class BdoDir extends AttrTransform
{
    /**
     * @param array               $attr
     * @param \HTMLPurifier\Config $config
     * @param Context             $context
     *
     * @return array
     * @throws \HTMLPurifier\Exception
     */
    public function transform(array $attr, \HTMLPurifier\Config $config, Context $context): array
    {
        if (isset($attr['dir'])) {
            return $attr;
        }
        $attr['dir'] = $config->get('Attr.DefaultTextDir');

        return $attr;
    }
}
