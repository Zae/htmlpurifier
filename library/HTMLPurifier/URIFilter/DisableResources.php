<?php

declare(strict_types=1);

use HTMLPurifier\Context;
use HTMLPurifier\URIFilter;
use HTMLPurifier\URI;

/**
 * Class HTMLPurifier_URIFilter_DisableResources
 */
class HTMLPurifier_URIFilter_DisableResources extends URIFilter
{
    /**
     * @type string
     */
    public $name = 'DisableResources';

    /**
     * @param URI                 $uri
     * @param HTMLPurifier_Config $config
     * @param Context             $context
     *
     * @return bool
     */
    public function filter(URI &$uri, HTMLPurifier_Config $config, Context $context): bool
    {
        return !$context->get('EmbeddedURI', true);
    }
}
