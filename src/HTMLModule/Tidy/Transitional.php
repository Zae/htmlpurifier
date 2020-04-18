<?php

declare(strict_types=1);

namespace HTMLPurifier\HTMLModule\Tidy;

/**
 * Class HTMLPurifier\HTMLModule\Tidy\HTMLPurifier_HTMLModule_Tidy_Transitional
 */
class Transitional extends XHTMLAndHTML4
{
    /**
     * @type string
     */
    public $name = 'Tidy\\Transitional';

    /**
     * @type string
     */
    public $defaultLevel = 'heavy';
}
