<?php

declare(strict_types=1);

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
     * @param HTMLPurifier_URI     $uri
     * @param HTMLPurifier_Config  $config
     * @param HTMLPurifier_Context $context
     *
     * @return bool
     */
    public function filter(HTMLPurifier_URI &$uri, HTMLPurifier_Config $config, HTMLPurifier_Context $context): bool
    {
        if (!$context->get('EmbeddedURI', true)) {
            return true;
        }

        return parent::filter($uri, $config, $context);
    }
}
