<?php

declare(strict_types=1);

use HTMLPurifier\Definition;
use HTMLPurifier\Context;
use HTMLPurifier\URIParser;
use HTMLPurifier\URIFilter;
use HTMLPurifier\URI;

/**
 * Class HTMLPurifier_URIDefinition
 */
class HTMLPurifier_URIDefinition extends Definition
{
    public $type = 'URI';
    protected $filters = [];
    protected $postFilters = [];
    protected $registeredFilters = [];

    /**
     * HTMLPurifier\HTMLPurifier_URI object of the base specified at %URI.Base
     */
    public $base;

    /**
     * String host to consider "home" base, derived off of $base
     */
    public $host;

    /**
     * Name of default scheme based on %URI.DefaultScheme and %URI.Base
     */
    public $defaultScheme;

    public function __construct()
    {
        $this->registerFilter(new HTMLPurifier_URIFilter_DisableExternal());
        $this->registerFilter(new HTMLPurifier_URIFilter_DisableExternalResources());
        $this->registerFilter(new HTMLPurifier_URIFilter_DisableResources());
        $this->registerFilter(new HTMLPurifier_URIFilter_HostBlacklist());
        $this->registerFilter(new HTMLPurifier_URIFilter_SafeIframe());
        $this->registerFilter(new HTMLPurifier_URIFilter_MakeAbsolute());
        $this->registerFilter(new HTMLPurifier_URIFilter_Munge());
    }

    /**
     * @param URIFilter $filter
     */
    public function registerFilter(URIFilter $filter)
    {
        $this->registeredFilters[$filter->name] = $filter;
    }

    /**
     * @param $filter
     * @param $config
     */
    public function addFilter(URIFilter $filter, HTMLPurifier_Config $config)
    {
        $r = $filter->prepare($config);

        if ($r === false) {
            return;
        } // null is ok, for backwards compat

        if ($filter->post) {
            $this->postFilters[$filter->name] = $filter;
        } else {
            $this->filters[$filter->name] = $filter;
        }
    }

    /**
     * Sets up the definition object into the final form, something
     * not done by the constructor
     *
     * @param HTMLPurifier_Config $config
     *
     * @throws HTMLPurifier_Exception
     */
    protected function doSetup(HTMLPurifier_Config $config): void
    {
        $this->setupMemberVariables($config);
        $this->setupFilters($config);
    }

    /**
     * @param HTMLPurifier_Config $config
     *
     * @throws HTMLPurifier_Exception
     */
    protected function setupFilters(HTMLPurifier_Config $config): void
    {
        foreach ($this->registeredFilters as $name => $filter) {
            if ($filter->always_load) {
                $this->addFilter($filter, $config);
            } else {
                $conf = $config->get('URI.' . $name);
                if ($conf !== false && $conf !== null) {
                    $this->addFilter($filter, $config);
                }
            }
        }

        unset($this->registeredFilters);
    }

    /**
     * @param HTMLPurifier_Config $config
     *
     * @throws HTMLPurifier_Exception
     */
    protected function setupMemberVariables(HTMLPurifier_Config $config): void
    {
        $this->host = $config->get('URI.Host');
        $base_uri = $config->get('URI.Base');
        if (!is_null($base_uri)) {
            $parser = new URIParser();
            $this->base = $parser->parse($base_uri);
            $this->defaultScheme = $this->base->scheme;

            if (is_null($this->host)) {
                $this->host = $this->base->host;
            }
        }

        if (is_null($this->defaultScheme)) {
            $this->defaultScheme = $config->get('URI.DefaultScheme');
        }
    }

    /**
     * @param HTMLPurifier_Config $config
     * @param Context             $context
     *
     * @return HTMLPurifier_URIScheme|null
     */
    public function getDefaultScheme(HTMLPurifier_Config $config, Context $context)
    {
        return HTMLPurifier_URISchemeRegistry::instance()->getScheme($this->defaultScheme, $config, $context);
    }

    /**
     * @param URI                 $uri
     * @param HTMLPurifier_Config $config
     * @param Context             $context
     *
     * @return bool
     */
    public function filter(URI &$uri, HTMLPurifier_Config $config, Context $context)
    {
        foreach ($this->filters as $name => $f) {
            $result = $f->filter($uri, $config, $context);
            if (!$result) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param URI                 $uri
     * @param HTMLPurifier_Config $config
     * @param Context             $context
     *
     * @return bool
     */
    public function postFilter(URI &$uri, HTMLPurifier_Config $config, Context $context)
    {
        foreach ($this->postFilters as $name => $f) {
            $result = $f->filter($uri, $config, $context);
            if (!$result) {
                return false;
            }
        }

        return true;
    }
}
