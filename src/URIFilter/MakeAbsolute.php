<?php

declare(strict_types=1);

// does not support network paths
namespace HTMLPurifier\URIFilter;

use HTMLPurifier\Context;
use HTMLPurifier\Exception;
use HTMLPurifier\URIFilter;
use HTMLPurifier\URI;
use HTMLPurifier\Config;
use HTMLPurifier\URIScheme;

use function is_null;

/**
 * Class HTMLPurifier\URIFilter\MakeAbsolute
 */
class MakeAbsolute extends URIFilter
{
    /**
     * @var string
     */
    public $name = 'MakeAbsolute';

    /**
     * @var URI|null
     */
    protected $base;

    /**
     * @var array
     */
    protected $basePathStack = [];

    /**
     * @param Config $config
     *
     * @return bool
     * @throws Exception
     */
    public function prepare(Config $config): bool
    {
        $def = $config->getDefinition('URI');

        if (is_null($def)) {
            return false;
        }

        $this->base = $def->base;

        if (is_null($this->base)) {
            trigger_error(
                'URI.MakeAbsolute is being ignored due to lack of ' .
                'value for URI.Base configuration',
                E_USER_WARNING
            );

            return false;
        }

        $this->base->fragment = null; // fragment is invalid for base URI
        $stack = explode('/', (string)$this->base->path);
        array_pop($stack); // discard last segment
        $stack = $this->collapseStack($stack); // do pre-parsing
        $this->basePathStack = $stack;

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
        if (is_null($this->base)) {
            return true;
        } // abort early

        if (
            $uri->path === ''
            && is_null($uri->scheme)
            && is_null($uri->host)
            && is_null($uri->query)
            && is_null($uri->fragment)
        ) {
            // reference to current document
            $uri = clone $this->base;

            return true;
        }

        if (!is_null($uri->scheme)) {
            // absolute URI already: don't change
            if (!is_null($uri->host)) {
                return true;
            }

            $scheme_obj = $uri->getSchemeObj($config, $context);
            if (!$scheme_obj instanceof URIScheme) {
                // scheme not recognized
                return false;
            }

            if (!$scheme_obj->hierarchical) {
                // non-hierarchal URI with explicit scheme, don't change
                return true;
            }
            // special case: had a scheme but always is hierarchical and had no authority
        }
        if (!is_null($uri->host)) {
            // network path, don't bother
            return true;
        }

        if ($uri->path === '') {
            $uri->path = $this->base->path;
        } elseif (!is_null($uri->path) && $uri->path[0] !== '/') {
            // relative path, needs more complicated processing
            $stack = explode('/', (string)$uri->path);
            $new_stack = array_merge($this->basePathStack, $stack);

            if ($new_stack[0] !== '' && !is_null($this->base->host)) {
                array_unshift($new_stack, '');
            }

            $new_stack = $this->collapseStack($new_stack);
            $uri->path = implode('/', $new_stack);
        } else {
            // absolute path, but still we should collapse
            $uri->path = implode('/', $this->collapseStack(explode('/', (string)$uri->path)));
        }

        // re-combine
        $uri->scheme = $this->base->scheme;
        if (is_null($uri->userinfo)) {
            $uri->userinfo = $this->base->userinfo;
        }

        /**
         * @psalm-suppress RedundantCondition
         */
        if (is_null($uri->host)) {
            $uri->host = $this->base->host;
        }

        if (is_null($uri->port)) {
            $uri->port = $this->base->port;
        }

        return true;
    }

    /**
     * Resolve dots and double-dots in a path stack
     *
     * @param array $stack
     *
     * @return array
     */
    private function collapseStack(array $stack): array
    {
        $result = [];
        $is_folder = false;
        for ($i = 0; isset($stack[$i]); $i++) {
            $is_folder = false;
            // absorb an internally duplicated slash
            if ($stack[$i] === '' && $i && isset($stack[$i + 1])) {
                continue;
            }

            if ($stack[$i] === '..') {
                if (!empty($result)) {
                    $segment = array_pop($result);
                    if ($segment === '' && empty($result)) {
                        // error case: attempted to back out too far:
                        // restore the leading slash
                        $result[] = '';
                    } elseif ($segment === '..') {
                        $result[] = '..'; // cannot remove .. with ..
                    }
                } else {
                    // relative path, preserve the double-dots
                    $result[] = '..';
                }

                $is_folder = true;
                continue;
            }

            if ($stack[$i] === '.') {
                // silently absorb
                $is_folder = true;
                continue;
            }

            $result[] = $stack[$i];
        }

        if ($is_folder) {
            $result[] = '';
        }

        return $result;
    }
}
