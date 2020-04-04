<?php

declare(strict_types=1);

/**
 * Class HTMLPurifier_HTMLModule_Tidy_Proprietary
 */
class HTMLPurifier_HTMLModule_Tidy_Proprietary extends HTMLPurifier_HTMLModule_Tidy
{
    /**
     * @type string
     */
    public $name = 'Tidy_Proprietary';

    /**
     * @type string
     */
    public $defaultLevel = 'light';

    /**
     * @return array
     */
    public function makeFixes(): array
    {
        $r = [];
        $r['table@background'] = new HTMLPurifier_AttrTransform_Background();
        $r['td@background'] = new HTMLPurifier_AttrTransform_Background();
        $r['th@background'] = new HTMLPurifier_AttrTransform_Background();
        $r['tr@background'] = new HTMLPurifier_AttrTransform_Background();
        $r['thead@background'] = new HTMLPurifier_AttrTransform_Background();
        $r['tfoot@background'] = new HTMLPurifier_AttrTransform_Background();
        $r['tbody@background'] = new HTMLPurifier_AttrTransform_Background();
        $r['table@height'] = new HTMLPurifier_AttrTransform_Length('height');

        return $r;
    }
}
