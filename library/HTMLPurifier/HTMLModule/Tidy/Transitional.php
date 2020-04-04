<?php

declare(strict_types=1);

/**
 * Class HTMLPurifier_HTMLModule_Tidy_Transitional
 */
class HTMLPurifier_HTMLModule_Tidy_Transitional extends HTMLPurifier_HTMLModule_Tidy_XHTMLAndHTML4
{
    /**
     * @type string
     */
    public $name = 'Tidy_Transitional';

    /**
     * @type string
     */
    public $defaultLevel = 'heavy';
}
