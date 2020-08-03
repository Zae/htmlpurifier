<?php

declare(strict_types=1);

namespace HTMLPurifier\HTMLModule\Tidy;

use HTMLPurifier\AttrTransform\Lang;
use HTMLPurifier\HTMLModule\Tidy;

/**
 * Class HTMLPurifier\HTMLModule\Tidy\HTMLPurifier_HTMLModule_Tidy_XHTML
 */
class XHTML extends Tidy
{
    /**
     * @var string
     */
    public $name = 'Tidy\\XHTML';

    /**
     * @var string
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
