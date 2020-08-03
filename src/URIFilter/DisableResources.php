<?php

declare(strict_types=1);

namespace HTMLPurifier\URIFilter;

use HTMLPurifier\Context;
use HTMLPurifier\URIFilter;
use HTMLPurifier\URI;
use HTMLPurifier\Config;

/**
 * Class HTMLPurifier\URIFilter\HTMLPurifier_URIFilter_DisableResources
 */
class DisableResources extends URIFilter
{
    /**
     * @var string
     */
    public $name = 'DisableResources';

    /**
     * @param URI     $uri
     * @param Config  $config
     * @param Context $context
     *
     * @return bool
     */
    public function filter(URI &$uri, Config $config, Context $context): bool
    {
        return !$context->get('EmbeddedURI', true);
    }
}
