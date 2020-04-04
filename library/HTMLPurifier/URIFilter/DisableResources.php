<?php

declare(strict_types=1);

/**
 * Class HTMLPurifier_URIFilter_DisableResources
 */
class HTMLPurifier_URIFilter_DisableResources extends HTMLPurifier_URIFilter
{
    /**
     * @type string
     */
    public $name = 'DisableResources';

    /**
     * @param HTMLPurifier_URI     $uri
     * @param HTMLPurifier_Config  $config
     * @param HTMLPurifier_Context $context
     *
     * @return bool
     */
    public function filter(HTMLPurifier_URI &$uri, HTMLPurifier_Config $config, HTMLPurifier_Context $context): bool
    {
        return !$context->get('EmbeddedURI', true);
    }
}
