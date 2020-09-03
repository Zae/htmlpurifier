<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef;

use HTMLPurifier\AttrDef;
use HTMLPurifier\Config;
use HTMLPurifier\Context;

use function is_null;

/**
 * Decorator that, depending on a token, switches between two definitions.
 */
class Switcher
{
    /**
     * @var string
     */
    protected $tag;

    /**
     * @var AttrDef
     */
    protected $withTag;

    /**
     * @var AttrDef
     */
    protected $withoutTag;

    /**
     * @param string  $tag         Tag name to switch upon
     * @param AttrDef $with_tag    Call if token matches tag
     * @param AttrDef $without_tag Call if token doesn't match, or there is no token
     */
    public function __construct(string $tag, AttrDef $with_tag, AttrDef $without_tag)
    {
        $this->tag = $tag;
        $this->withTag = $with_tag;
        $this->withoutTag = $without_tag;
    }

    /**
     * @param string       $string
     * @param Config|null  $config
     * @param Context|null $context
     *
     * @return bool|string|null
     * @throws \HTMLPurifier\Exception
     */
    public function validate(string $string, ?Config $config, ?Context $context)
    {
        if (!is_null($context)) {
            $token = $context->get('CurrentToken', true);
        }

        if (!isset($token) || !$token || $token->name !== $this->tag) {
            return $this->withoutTag->validate($string, $config, $context);
        }

        return $this->withTag->validate($string, $config, $context);
    }
}
