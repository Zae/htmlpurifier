<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef;

use HTMLPurifier\AttrDef;
use HTMLPurifier_Config;
use HTMLPurifier_Context;

/**
 * Decorator that, depending on a token, switches between two definitions.
 */
class Switcher
{
    /**
     * @type string
     */
    protected $tag;

    /**
     * @type AttrDef
     */
    protected $withTag;

    /**
     * @type AttrDef
     */
    protected $withoutTag;

    /**
     * @param string  $tag         Tag name to switch upon
     * @param AttrDef $with_tag    Call if token matches tag
     * @param AttrDef $without_tag Call if token doesn't match, or there is no token
     */
    public function __construct($tag, $with_tag, $without_tag)
    {
        $this->tag = $tag;
        $this->withTag = $with_tag;
        $this->withoutTag = $without_tag;
    }

    /**
     * @param string               $string
     * @param HTMLPurifier_Config  $config
     * @param HTMLPurifier_Context $context
     *
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        $token = $context->get('CurrentToken', true);

        if (!$token || $token->name !== $this->tag) {
            return $this->withoutTag->validate($string, $config, $context);
        }

        return $this->withTag->validate($string, $config, $context);
    }
}
