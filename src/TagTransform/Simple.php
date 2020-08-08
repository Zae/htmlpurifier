<?php

declare(strict_types=1);

namespace HTMLPurifier\TagTransform;

use HTMLPurifier\Context;
use HTMLPurifier\TagTransform;
use HTMLPurifier\Token\Tag;
use HTMLPurifier\Token\Start;
use HTMLPurifier\Config;
use HTMLPurifier\Token\EmptyToken;

use function is_null;

/**
 * Simple transformation, just change tag name to something else,
 * and possibly add some styling. This will cover most of the deprecated
 * tag cases.
 */
class Simple extends TagTransform
{
    /**
     * @var string|null
     */
    protected $style;

    /**
     * @param string $transform_to Tag name to transform to.
     * @param string $style        CSS style to add to the tag
     */
    public function __construct(string $transform_to, string $style = null)
    {
        $this->transform_to = $transform_to;
        $this->style = $style;
    }

    /**
     * @param Tag     $tag
     * @param Config  $config
     * @param Context $context
     *
     * @return Tag
     */
    public function transform(Tag $tag, Config $config, Context $context)
    {
        $new_tag = clone $tag;
        $new_tag->name = $this->transform_to;
        if (
            ($new_tag instanceof Start || $new_tag instanceof EmptyToken)
            && !is_null($this->style)
        ) {
            $this->prependCSS($new_tag->attr, $this->style);
        }

        return $new_tag;
    }
}
