<?php

declare(strict_types=1);

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
        $r['@lang'] = new HTMLPurifier_AttrTransform_Lang();
        return $r;
    }
}
