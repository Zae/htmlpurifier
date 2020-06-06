<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\HTML;

use HTMLPurifier\AttrDef\Enum;
use HTMLPurifier\Config;
use HTMLPurifier\Context;
use HTMLPurifier\Exception;

/**
 * Special-case enum attribute definition that lazy loads allowed frame targets
 */
class FrameTarget extends Enum
{
    /**
     * @type array
     */
    public $valid_values = false; // uninitialized value

    /**
     * @type bool
     */
    protected $case_sensitive = false;

    public function __construct()
    {
    }

    /**
     * @param string  $string
     * @param Config  $config
     * @param Context $context
     *
     * @return bool|string
     * @throws Exception
     */
    public function validate(string $string, ?Config $config, ?Context $context)
    {
        if ($this->valid_values === false) {
            $this->valid_values = $config->get('Attr.AllowedFrameTargets');
        }

        return parent::validate($string, $config, $context);
    }
}
