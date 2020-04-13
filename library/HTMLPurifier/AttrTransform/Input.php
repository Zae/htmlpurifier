<?php

declare(strict_types=1);

use HTMLPurifier\AttrDef\HTML\Pixels;
use HTMLPurifier\Context;
use HTMLPurifier\AttrTransform;

/**
 * Performs miscellaneous cross attribute validation and filtering for
 * input elements. This is meant to be a post-transform.
 */
class HTMLPurifier_AttrTransform_Input extends AttrTransform
{
    /**
     * @type Pixels
     */
    protected $pixels;

    public function __construct()
    {
        $this->pixels = new Pixels();
    }

    /**
     * @param array               $attr
     * @param HTMLPurifier_Config $config
     * @param Context             $context
     *
     * @return array
     */
    public function transform(array $attr, HTMLPurifier_Config $config, Context $context): array
    {
        if (!isset($attr['type'])) {
            $t = 'text';
        } else {
            $t = strtolower($attr['type']);
        }

        if (isset($attr['checked']) && $t !== 'radio' && $t !== 'checkbox') {
            unset($attr['checked']);
        }

        if (isset($attr['maxlength']) && $t !== 'text' && $t !== 'password') {
            unset($attr['maxlength']);
        }

        if (isset($attr['size']) && $t !== 'text' && $t !== 'password') {
            $result = $this->pixels->validate($attr['size'], $config, $context);
            if ($result === false) {
                unset($attr['size']);
            } else {
                $attr['size'] = $result;
            }
        }

        if (isset($attr['src']) && $t !== 'image') {
            unset($attr['src']);
        }

        if (!isset($attr['value']) && ($t === 'radio' || $t === 'checkbox')) {
            $attr['value'] = '';
        }

        return $attr;
    }
}
