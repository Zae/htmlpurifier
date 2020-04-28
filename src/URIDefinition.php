<?php

declare(strict_types=1);

namespace HTMLPurifier;

use HTMLPurifier\URIFilter\DisableExternal;
use HTMLPurifier\URIFilter\DisableExternalResources;
use HTMLPurifier\URIFilter\DisableResources;
use HTMLPurifier\URIFilter\HostBlacklist;
use HTMLPurifier\URIFilter\MakeAbsolute;
use HTMLPurifier\URIFilter\Munge;
use HTMLPurifier\URIFilter\SafeIframe;

/**
 * Class HTMLPurifier\HTMLPurifier_URIDefinition
 */
class URIDefinition extends Definition
{
    /**
     * @var string
     */
    public $type = 'URI';

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var array
     */
    protected $postFilters = [];

    /**
     * @var array
     */
    protected $registeredFilters = [];

    /**
     * @var URI|null object of the base specified at %URI.Base
     */
    public $base;

    /**
     * @var string|null host to consider "home" base, derived off of $base
     */
    public $host;

    /**
     * @var string|null Name of default scheme based on %URI.DefaultScheme and %URI.Base
     */
    public $defaultScheme;

    public function __construct()
    {
        $this->registerFilter(new DisableExternal());
        $this->registerFilter(new DisableExternalResources());
        $this->registerFilter(new DisableResources());
        $this->registerFilter(new HostBlacklist());
        $this->registerFilter(new SafeIframe());
        $this->registerFilter(new MakeAbsolute());
        $this->registerFilter(new Munge());
    }

    /**
     * @param URIFilter $filter
     */
    public function registerFilter(URIFilter $filter): void
    {
        $this->registeredFilters[$filter->name] = $filter;
    }

    /**
     * @param $filter
     * @param $config
     */
    public function addFilter(URIFilter $filter, \HTMLPurifier\Config $config): void
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
     * @param Config $config
     *
     * @throws Exception
     */
    protected function doSetup(\HTMLPurifier\Config $config): void
    {
        $this->setupMemberVariables($config);
        $this->setupFilters($config);
    }

    /**
     * @param Config $config
     *
     * @throws Exception
     */
    protected function setupFilters(\HTMLPurifier\Config $config): void
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
     * @param Config $config
     *
     * @throws Exception
     */
    protected function setupMemberVariables(\HTMLPurifier\Config $config): void
    {
        $this->host = $config->get('URI.Host');
        $base_uri = $config->get('URI.Base');

        if (!\is_null($base_uri)) {
            $parser = new URIParser();
            $this->base = $parser->parse($base_uri);

            if ($this->base instanceof URI) {
                $this->defaultScheme = $this->base->scheme;

                if (\is_null($this->host)) {
                    $this->host = $this->base->host;
                }
            }
        }

        if (\is_null($this->defaultScheme)) {
            $this->defaultScheme = $config->get('URI.DefaultScheme');
        }
    }

    /**
     * @param Config  $config
     * @param Context $context
     *
     * @return URIScheme|null
     */
    public function getDefaultScheme(\HTMLPurifier\Config $config, Context $context): ?URIScheme
    {
        return URISchemeRegistry::instance()->getScheme($this->defaultScheme, $config, $context);
    }

    /**
     * @param URI     $uri
     * @param Config  $config
     * @param Context $context
     *
     * @return bool
     */
    public function filter(URI &$uri, \HTMLPurifier\Config $config, Context $context): bool
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
     * @param URI     $uri
     * @param Config  $config
     * @param Context $context
     *
     * @return bool
     */
    public function postFilter(URI &$uri, \HTMLPurifier\Config $config, Context $context): bool
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
