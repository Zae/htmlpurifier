<?php

declare(strict_types=1);

namespace HTMLPurifier\URIFilter;

use HTMLPurifier\Config;
use HTMLPurifier\Context;
use HTMLPurifier\Exception;
use HTMLPurifier\URIParser;
use HTMLPurifier\URIFilter;
use HTMLPurifier\URI;
use HTMLPurifier\URIScheme;

/**
 * Class HTMLPurifier\URIFilter\HTMLPurifier_URIFilter_Munge
 */
class Munge extends URIFilter
{
    /**
     * @type string
     */
    public $name = 'Munge';

    /**
     * @type bool
     */
    public $post = true;

    /**
     * @type string
     */
    private $target;

    /**
     * @type URIParser
     */
    private $parser;

    /**
     * @type bool
     */
    private $doEmbed;

    /**
     * @type string
     */
    private $secretKey;

    /**
     * @type array
     */
    protected $replace = [];

    /**
     * @param Config $config
     *
     * @return bool
     * @throws \HTMLPurifier\Exception
     */
    public function prepare(Config $config): bool
    {
        $this->target = $config->get('URI.' . $this->name);
        $this->parser = new URIParser();
        $this->doEmbed = $config->get('URI.MungeResources');
        $this->secretKey = $config->get('URI.MungeSecretKey');

        if ($this->secretKey && !\function_exists('hash_hmac')) {
            throw new Exception('Cannot use %URI.MungeSecretKey without hash_hmac support.');
        }

        return true;
    }

    /**
     * @param URI     $uri
     * @param Config  $config
     * @param Context $context
     *
     * @return bool
     * @throws Exception
     */
    public function filter(URI &$uri, Config $config, Context $context): bool
    {
        if ($context->get('EmbeddedURI', true) && !$this->doEmbed) {
            return true;
        }

        $scheme_obj = $uri->getSchemeObj($config, $context);
        if (!$scheme_obj instanceof URIScheme) {
            return true;
        } // ignore unknown schemes, maybe another postfilter did it

        if (!$scheme_obj->browsable) {
            return true;
        } // ignore non-browseable schemes, since we can't munge those in a reasonable way

        if ($uri->isBenign($config, $context)) {
            return true;
        } // don't redirect if a benign URL

        $this->makeReplace($uri, $config, $context);
        $this->replace = array_map('rawurlencode', $this->replace);

        $new_uri = strtr($this->target, $this->replace);
        $new_uri = $this->parser->parse($new_uri);
        // don't redirect if the target host is the same as the
        // starting host

        if ($uri->host === $new_uri->host) {
            return true;
        }

        $uri = $new_uri; // overwrite

        return true;
    }

    /**
     * @param URI                 $uri
     * @param Config $config
     * @param Context             $context
     */
    protected function makeReplace(
        URI $uri,
        Config $config,
        Context $context
    ): void {
        $string = $uri->toString();

        // always available
        $this->replace['%s'] = $string;
        $this->replace['%r'] = $context->get('EmbeddedURI', true);
        $token = $context->get('CurrentToken', true);

        $this->replace['%n'] = $token ? $token->name : null;
        $this->replace['%m'] = $context->get('CurrentAttr', true);
        $this->replace['%p'] = $context->get('CurrentCSSProperty', true);

        // not always available
        if ($this->secretKey) {
            $this->replace['%t'] = hash_hmac('sha256', $string, $this->secretKey);
        }
    }
}
