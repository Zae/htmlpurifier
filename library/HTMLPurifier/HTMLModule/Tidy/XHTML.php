<?php

declare(strict_types=1);

use HTMLPurifier\AttrTransform\Lang;

/**
 * Class HTMLPurifier_HTMLModule_Tidy_XHTML
 */
class HTMLPurifier_HTMLModule_Tidy_XHTML extends HTMLPurifier_HTMLModule_Tidy
{
    /**
     * @type string
     */
    public $name = 'Tidy_XHTML';

    /**
     * @type string
     */
    public $defaultLevel = 'medium';

    /**
     * @return array
     */
    public function makeFixes(): array
    {
        $r = [];
        $r['@lang'] = new Lang();
        return $r;
    }
}
