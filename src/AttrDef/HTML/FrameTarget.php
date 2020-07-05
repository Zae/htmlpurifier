<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\HTML;

use HTMLPurifier\AttrDef\Enum;
use HTMLPurifier\Config;
use HTMLPurifier\Context;
use HTMLPurifier\Exception;

use function is_null;

/**
 * Special-case enum attribute definition that lazy loads allowed frame targets
 */
class FrameTarget extends Enum
{
    /**
     * @var array|bool
     */
    public $valid_values = false; // uninitialized value

    /**
     * @var bool
     */
    protected $case_sensitive = false;

    public function __construct()
    {
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
        if ($this->valid_values === false && !is_null($config)) {
            $this->valid_values = $config->get('Attr.AllowedFrameTargets');
        }

        return parent::validate($string, $config, $context);
    }
}
