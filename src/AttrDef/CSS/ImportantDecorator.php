<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\CSS;

use HTMLPurifier\AttrDef;
use HTMLPurifier\AttrDef\AttrDefValidate;
use HTMLPurifier\Config;
use HTMLPurifier\Context;

use function strlen;

/**
 * Decorator which enables !important to be used in CSS values.
 */
class ImportantDecorator extends AttrDef
{
    /**
     * @var AttrDefValidate
     */
    public $def;

    /**
     * @var bool
     */
    public $allow;

    /**
     * @param AttrDefValidate $def   Definition to wrap
     * @param bool                    $allow Whether or not to allow !important
     */
    public function __construct(AttrDefValidate $def, bool $allow = false)
    {
        $this->def = $def;
        $this->allow = $allow;
    }

    /**
     * Intercepts and removes !important if necessary
     *
     * @param string       $string
     * @param Config|null  $config
     * @param Context|null $context
     *
     * @return bool|string|null
     */
    public function validate(string $string, ?Config $config, ?Context $context)
    {
        // test for ! and important tokens
        $string = trim($string);
        $is_important = false;

        // :TODO: optimization: test directly for !important and ! important
        if (strlen($string) >= 9 && substr($string, -9) === 'important') {
            $temp = rtrim(substr($string, 0, -9));
            // use a temp, because we might want to restore important
            if ($temp !== '' && substr($temp, -1) === '!') {
                $string = rtrim(substr($temp, 0, -1));
                $is_important = true;
            }
        }

        $string = $this->def->validate($string, $config, $context);

        if ($this->allow && $is_important) {
            $string .= ' !important';
        }

        return $string;
    }
}
