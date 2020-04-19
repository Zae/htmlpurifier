<?php

declare(strict_types=1);

// Enum = Enumerated
namespace HTMLPurifier\AttrDef;

use HTMLPurifier\AttrDef;
use \HTMLPurifier\Config;
use HTMLPurifier\Context;

/**
 * Validates a keyword against a list of valid values.
 *
 * @warning The case-insensitive compare of this function uses PHP's
 *          built-in strtolower and ctype_lower functions, which may
 *          cause problems with international comparisons
 */
class Enum extends AttrDef
{
    /**
     * Lookup table of valid values.
     *
     * @type array
     * @todo Make protected
     */
    public $valid_values = [];

    /**
     * Bool indicating whether or not enumeration is case sensitive.
     *
     * @note In general this is always case insensitive.
     */
    protected $case_sensitive = false; // values according to W3C spec

    /**
     * @param array $valid_values   List of valid values
     * @param bool  $case_sensitive Whether or not case sensitive
     */
    public function __construct($valid_values = [], $case_sensitive = false)
    {
        $this->valid_values = array_flip($valid_values);
        $this->case_sensitive = $case_sensitive;
    }

    /**
     * @param string                $string
     * @param \HTMLPurifier\Config   $config
     * @param \HTMLPurifier\Context $context
     *
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        $string = trim($string);
        if (!$this->case_sensitive) {
            // we may want to do full case-insensitive libraries
            $string = ctype_lower($string) ? $string : strtolower($string);
        }

        $result = isset($this->valid_values[$string]);

        return $result ? $string : false;
    }

    /**
     * @param string $string In form of comma-delimited list of case-insensitive
     *                       valid values. Example: "foo,bar,baz". Prepend "s:" to make
     *                       case sensitive
     *
     * @return Enum
     */
    public function make($string)
    {
        if (strlen($string) > 2 && $string[0] === 's' && $string[1] === ':') {
            $string = substr($string, 2);
            $sensitive = true;
        } else {
            $sensitive = false;
        }

        $values = explode(',', $string);

        return new Enum($values, $sensitive);
    }
}
