<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\CSS;

use HTMLPurifier\AttrDef\Integer;
use HTMLPurifier\AttrDef;
use \HTMLPurifier\Config;
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
     * @type \HTMLPurifier\AttrDef\Integer
     */
    protected $intValidator;

    public function __construct()
    {
        $this->intValidator = new Integer();
    }

    /**
     * @param string              $value
     * @param \HTMLPurifier\Config $config
     * @param Context             $context
     *
     * @return bool|string
     */
    public function validate($value, $config, $context)
    {
        $value = $this->parseCDATA($value);

        if ($value === 'none') {
            return $value;
        }

        // if we looped this we could support multiple filters
        $function_length = strcspn($value, '(');
        $function = trim(substr($value, 0, $function_length));
        if ($function !== 'alpha' &&
            $function !== 'Alpha' &&
            $function !== 'progid:DXImageTransform.Microsoft.Alpha'
        ) {
            return false;
        }

        $cursor = $function_length + 1;
        $parameters_length = strcspn($value, ')', $cursor);
        $parameters = substr($value, $cursor, $parameters_length);
        $params = explode(',', $parameters);
        $ret_params = [];
        $lookup = [];

        foreach ($params as $param) {
            [$key, $value] = explode('=', $param);
            $key = trim($key);
            $value = trim($value);

            if (isset($lookup[$key])) {
                continue;
            }

            if ($key !== 'opacity') {
                continue;
            }

            $value = $this->intValidator->validate($value, $config, $context);
            if ($value === false) {
                continue;
            }

            $int = (int)$value;
            if ($int > 100) {
                $value = '100';
            }

            if ($int < 0) {
                $value = '0';
            }

            $ret_params[] = "$key=$value";
            $lookup[$key] = true;
        }

        $ret_parameters = implode(',', $ret_params);

        return "$function($ret_parameters)";
    }
}
