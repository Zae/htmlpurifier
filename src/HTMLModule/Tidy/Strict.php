<?php

declare(strict_types=1);

namespace HTMLPurifier\HTMLModule\Tidy;

use HTMLPurifier\ChildDef;
use HTMLPurifier\ChildDef\StrictBlockquote;
use HTMLPurifier\ElementDef;
use HTMLPurifier\HTMLModule\Tidy\XHTMLAndHTML4;

/**
 * Class HTMLPurifier\HTMLModule\Tidy\HTMLPurifier_HTMLModule_Tidy_Strict
 */
class Strict extends XHTMLAndHTML4
{
    /**
     * @type string
     */
    public $name = 'Tidy\\Strict';

    /**
     * @type string
     */
    public $defaultLevel = 'light';

    /**
     * @return array
     */
    public function makeFixes(): array
    {
        $r = parent::makeFixes();
        $r['blockquote#content_model_type'] = 'strictblockquote';

        return $r;
    }

    /**
     * @type bool
     */
    public $defines_child_def = true;

    /**
     * @param ElementDef $def
     *
     * @return StrictBlockquote|ChildDef|null
     */
    public function getChildDef(ElementDef $def): ?ChildDef
    {
        if ($def->content_model_type !== 'strictblockquote') {
            return parent::getChildDef($def);
        }

        if (!\is_null($def->content_model)) {
            return new StrictBlockquote($def->content_model);
        }

        return null;
    }
}
