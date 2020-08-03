<?php

declare(strict_types=1);

namespace HTMLPurifier\HTMLModule\Tidy;

use HTMLPurifier\HTMLModule\Tidy;

/**
 * Name is deprecated, but allowed in strict doctypes, so onl
 */
class Name extends Tidy
{
    /**
     * @var string
     */
    public $name = 'Tidy\\Name';

    /**
     * @var string
     */
    public $defaultLevel = 'heavy';

    /**
     * @return array
     */
    public function makeFixes(): array
    {
        $r = [];
        // @name for img, a -----------------------------------------------
        // Technically, it's allowed even on strict, so we allow authors to use
        // it. However, it's deprecated in future versions of XHTML.
        $r['img@name'] = $r['a@name'] = new \HTMLPurifier\AttrTransform\Name();

        return $r;
    }
}
