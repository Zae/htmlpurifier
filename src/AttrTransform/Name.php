<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrTransform;

use HTMLPurifier\Context;
use HTMLPurifier\AttrTransform;
use HTMLPurifier\Config;
use HTMLPurifier\Exception;

/**
 * Pre-transform that changes deprecated name attribute to ID if necessary
 */
class Name extends AttrTransform
{
    /**
     * @param array   $attr
     * @param Config  $config
     * @param Context $context
     *
     * @return array
     * @throws Exception
     */
    public function transform(array $attr, Config $config, Context $context): array
    {
        // Abort early if we're using relaxed definition of name
        if ($config->get('HTML.Attr.Name.UseCDATA')) {
            return $attr;
        }

        if (!isset($attr['name'])) {
            return $attr;
        }

        $id = $this->confiscateAttr($attr, 'name');
        if (isset($attr['id'])) {
            return $attr;
        }

        $attr['id'] = $id;

        return $attr;
    }
}
