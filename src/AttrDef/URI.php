<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef;

use HTMLPurifier\AttrDef;
use HTMLPurifier\Config;
use HTMLPurifier\Context;
use HTMLPurifier\Exception;
use HTMLPurifier\URIDefinition;
use HTMLPurifier\URIParser;
use HTMLPurifier\URIScheme;

use function is_null;

/**
 * Validates a URI as defined by RFC 3986.
 *
 * @note Scheme-specific mechanics deferred to HTMLPurifier\HTMLPurifier_URIScheme
 */
class URI extends AttrDef
{
    /**
     * @type URIParser
     */
    protected $parser;

    /**
     * @type bool
     */
    protected $embedsResource;

    /**
     * @param bool $embeds_resource Does the URI here result in an extra HTTP request?
     */
    public function __construct(bool $embeds_resource = false)
    {
        $this->parser = new URIParser();
        $this->embedsResource = (bool)$embeds_resource;
    }

    /**
     * @param string $string
     *
     * @return URI
     */
    public function make(string $string): AttrDef
    {
        $embeds = ($string === 'embedded');

        return new URI($embeds);
    }

    /**
     * @param string       $string
     * @param Config|null  $config
     * @param Context|null $context
     *
     * @return bool|string
     * @throws Exception
     */
    public function validate(string $string, ?Config $config, ?Context $context)
    {
        if (is_null($config) || $config->get('URI.Disable')) {
            return false;
        }

        $string = $this->parseCDATA($string);

        // parse the URI
        $string = $this->parser->parse($string);
        if ($string === false) {
            return false;
        }

        // add embedded flag to context for validators
        if (!is_null($context)) {
            $context->register('EmbeddedURI', $this->embedsResource);
        }

        $ok = false;
        do {
            /**
             * @psalm-suppress RedundantCondition
             * @psalm-suppress TypeDoesNotContainNull
             */
            if (is_null($config) || is_null($context)) {
                break;
            }

            // generic validation
            $result = $string->validate($config, $context);
            if (!$result) {
                break;
            }

            // chained filtering
            /** @var URIDefinition $uri_def */
            $uri_def = $config->getDefinition('URI');
            $result = $uri_def->filter($string, $config, $context);
            if (!$result) {
                break;
            }

            // scheme-specific validation
            $scheme_obj = $string->getSchemeObj($config, $context);
            if (!$scheme_obj instanceof URIScheme) {
                break;
            }

            if ($this->embedsResource && !$scheme_obj->browsable) {
                break;
            }

            $result = $scheme_obj->validate($string, $config, $context);
            if (!$result) {
                break;
            }

            // Post chained filtering
            $result = $uri_def->postFilter($string, $config, $context);
            if (!$result) {
                break;
            }

            // survived gauntlet
            $ok = true;
        } while (false);

        if (!is_null($context)) {
            $context->destroy('EmbeddedURI');
        }

        if (!$ok) {
            return false;
        }

        // back to string
        return $string->toString();
    }
}
