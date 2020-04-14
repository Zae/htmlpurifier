<?php

declare(strict_types=1);

use HTMLPurifier\AttrTransform\Background;
use HTMLPurifier\AttrTransform\Length;

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
        $r['table@background'] = new Background();
        $r['td@background'] = new Background();
        $r['th@background'] = new Background();
        $r['tr@background'] = new Background();
        $r['thead@background'] = new Background();
        $r['tfoot@background'] = new Background();
        $r['tbody@background'] = new Background();
        $r['table@height'] = new Length('height');

        return $r;
    }
}
