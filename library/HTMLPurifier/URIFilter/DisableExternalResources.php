<?php

declare(strict_types=1);

use HTMLPurifier\Context;
use HTMLPurifier\URI;

/**
 * Class HTMLPurifier_URIFilter_DisableExternalResources
 */
class HTMLPurifier_URIFilter_DisableExternalResources extends HTMLPurifier_URIFilter_DisableExternal
{
    /**
     * @type string
     */
    public $name = 'DisableExternalResources';

    /**
     * @param URI                 $uri
     * @param HTMLPurifier_Config $config
     * @param Context             $context
     *
     * @return bool
     */
    public function filter(URI &$uri, HTMLPurifier_Config $config, Context $context): bool
    {
        if (!$context->get('EmbeddedURI', true)) {
            return true;
        }

        return parent::filter($uri, $config, $context);
    }
}
