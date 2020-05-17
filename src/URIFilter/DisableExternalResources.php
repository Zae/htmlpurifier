<?php

declare(strict_types=1);

namespace HTMLPurifier\URIFilter;

use HTMLPurifier\Config;
use HTMLPurifier\Context;
use HTMLPurifier\URI;

/**
 * Class HTMLPurifier\URIFilter\HTMLPurifier_URIFilter_DisableExternalResources
 */
class DisableExternalResources extends DisableExternal
{
    /**
     * @type string
     */
    public $name = 'DisableExternalResources';

    /**
     * @param URI                 $uri
     * @param Config $config
     * @param Context             $context
     *
     * @return bool
     */
    public function filter(URI &$uri, Config $config, Context $context): bool
    {
        if (!$context->get('EmbeddedURI', true)) {
            return true;
        }

        return parent::filter($uri, $config, $context);
    }
}
