<?php

declare(strict_types=1);

namespace HTMLPurifier\HTMLModule\Tidy;

/**
 * Class HTMLPurifier\HTMLModule\Tidy\HTMLPurifier_HTMLModule_Tidy_Transitional
 */
class Transitional extends XHTMLAndHTML4
{
    /**
     * @var string
     */
    public $name = 'Tidy\\Transitional';

    /**
     * @var string
     */
    public $defaultLevel = 'heavy';
}
