<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\HTML;

use HTMLPurifier\AttrDef;
use HTMLPurifier\Config;
use HTMLPurifier\Context;
use HTMLPurifier\Exception;
use function is_null;

/**
 * Validates the HTML attribute ID.
 *
 * @warning Even though this is the id processor, it
 *          will ignore the directive Attr:IDBlacklist, since it will only
 *          go according to the ID accumulator. Since the accumulator is
 *          automatically generated, it will have already absorbed the
 *          blacklist. If you're hacking around, make sure you use load()!
 */
class ID extends AttrDef
{
    // selector is NOT a valid thing to use for IDREFs, because IDREFs
    // *must* target IDs that exist, whereas selector #ids do not.

    /**
     * Determines whether or not we're validating an ID in a CSS
     * selector context.
     *
     * @type bool
     */
    protected $selector;

    /**
     * @param bool $selector
     */
    public function __construct(bool $selector = false)
    {
        $this->selector = $selector;
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
        if (is_null($config) || (!$this->selector && !$config->get('Attr.EnableID'))) {
            return false;
        }

        $string = trim($string); // trim it first

        if ($string === '') {
            return false;
        }

        $prefix = $config->get('Attr.IDPrefix');
        if ($prefix !== '') {
            $prefix .= $config->get('Attr.IDPrefixLocal');
            // prevent re-appending the prefix
            if (strpos($string, $prefix) !== 0) {
                $string = $prefix . $string;
            }
        } elseif ($config->get('Attr.IDPrefixLocal') !== '') {
            trigger_error(
                '%Attr.IDPrefixLocal cannot be used unless ' .
                '%Attr.IDPrefix is set',
                E_USER_WARNING
            );
        }

        if (!$this->selector && !is_null($context)) {
            $id_accumulator =& $context->get('IDAccumulator');
            if (isset($id_accumulator->ids[$string])) {
                return false;
            }
        }

        // we purposely avoid using regex, hopefully this is faster

        if ($config->get('Attr.ID.HTML5') === true) {
            if (preg_match('/[\t\n\x0b\x0c ]/', $string)) {
                return false;
            }
        } else {
            if (ctype_alpha($string)) {
                // OK
            } else {
                if (!ctype_alpha(@$string[0])) {
                    return false;
                }

                // primitive style of regexps, I suppose
                $trim = trim(
                    $string,
                    'A..Za..z0..9:-._'
                );

                if ($trim !== '') {
                    return false;
                }
            }
        }

        $regexp = $config->get('Attr.IDBlacklistRegexp');
        if ($regexp && preg_match($regexp, $string)) {
            return false;
        }

        if (!$this->selector && isset($id_accumulator)) {
            $id_accumulator->add($string);
        }

        // if no change was made to the ID, return the result
        // else, return the new id if stripping whitespace made it
        //     valid, or return false.
        return $string;
    }
}
