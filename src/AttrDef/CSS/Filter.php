<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\CSS;

use HTMLPurifier\AttrDef\Integer;
use HTMLPurifier\AttrDef;
use HTMLPurifier\Config;
use HTMLPurifier\Context;

/**
 * Microsoft's proprietary filter: CSS property
 *
 * @note Currently supports the alpha filter. In the future, this will
 *       probably need an extensible framework
 */
class Filter extends AttrDef
{
    /**
     * @type Integer
     */
    protected $intValidator;

    public function __construct()
    {
        $this->intValidator = new Integer();
    }

    /**
     * @param string               $string
     * @param Config $config
     * @param Context              $context
     *
     * @return bool|string
     */
    public function validate(string $string, ?Config $config, ?Context $context)
    {
        $string = $this->parseCDATA($string);

        if ($string === 'none') {
            return $string;
        }

        // if we looped this we could support multiple filters
        $function_length = strcspn($string, '(');
        $function = trim(substr($string, 0, $function_length));
        if (
            $function !== 'alpha'
            && $function !== 'Alpha'
            && $function !== 'progid:DXImageTransform.Microsoft.Alpha'
        ) {
            return false;
        }

        $cursor = $function_length + 1;
        $parameters_length = strcspn($string, ')', $cursor);
        $parameters = substr($string, $cursor, $parameters_length);
        $params = explode(',', $parameters);
        $ret_params = [];
        $lookup = [];

        foreach ($params as $param) {
            [$key, $string] = explode('=', $param);
            $key = trim($key);
            $string = trim($string);

            if (isset($lookup[$key])) {
                continue;
            }

            if ($key !== 'opacity') {
                continue;
            }

            $string = $this->intValidator->validate($string, $config, $context);
            if ($string === false) {
                continue;
            }

            $int = (int)$string;
            if ($int > 100) {
                $string = '100';
            }

            if ($int < 0) {
                $string = '0';
            }

            $ret_params[] = "$key=$string";
            $lookup[$key] = true;
        }

        $ret_parameters = implode(',', $ret_params);

        return "$function($ret_parameters)";
    }
}
