<?php

declare(strict_types=1);

namespace HTMLPurifier\HTMLModule\Tidy;

use HTMLPurifier\AttrTransform\Background;
use HTMLPurifier\AttrTransform\Length;
use HTMLPurifier\HTMLModule\Tidy;

/**
 * Class HTMLPurifier\HTMLModule\Tidy\HTMLPurifier_HTMLModule_Tidy_Proprietary
 */
class Proprietary extends Tidy
{
    /**
     * @var string
     */
    public $name = 'Tidy\\Proprietary';

    /**
     * @var string|null
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
