<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\HTML;
use HTMLPurifier\AttrDef\HTML\Nmtokens;
use \HTMLPurifier\Config;
use HTMLPurifier\Context;
use HTMLPurifier\Exception;
use HTMLPurifier\HTMLDefinition;

/**
 * Implements special behavior for class attribute (normally NMTOKENS)
 */
class Classname extends Nmtokens
{
    /**
     * @param string  $string
     * @param Config  $config
     * @param Context $context
     *
     * @return string[]|false
     * @throws Exception
     */
    protected function split($string, $config, $context)
    {
        // really, this twiddle should be lazy loaded
        /** @var HTMLDefinition $def */
        $def = $config->getDefinition('HTML');
        $name = $def->doctype->name ?? '';
        if ($name === 'XHTML 1.1' || $name === 'XHTML 2.0') {
            return parent::split($string, $config, $context);
        }

        return preg_split('/\s+/', $string);
    }

    /**
     * @param array   $tokens
     * @param Config  $config
     * @param Context $context
     *
     * @return array
     * @throws Exception
     */
    protected function filter($tokens, $config, $context): array
    {
        $allowed = $config->get('Attr.AllowedClasses');
        $forbidden = $config->get('Attr.ForbiddenClasses');
        $ret = [];

        foreach ($tokens as $token) {
            if (($allowed === null || isset($allowed[$token])) 
                && !isset($forbidden[$token]) 
                // We need this O(n) check because of PHP's array
                // implementation that casts -0 to 0.
                && !\in_array($token, $ret, true)
            ) {
                $ret[] = $token;
            }
        }

        return $ret;
    }
}
