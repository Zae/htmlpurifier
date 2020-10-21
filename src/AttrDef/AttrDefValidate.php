<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef;

use HTMLPurifier\Config;
use HTMLPurifier\Context;

/**
 * Interface AttrDefValidate
 *
 * @package HTMLPurifier\AttrDef
 */
interface AttrDefValidate
{
    /**
     * @param string       $string
     * @param Config|null  $config
     * @param Context|null $context
     *
     * @return mixed
     */
    public function validate(string $string, ?Config $config, ?Context $context);
}
