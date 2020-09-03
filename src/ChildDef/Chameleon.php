<?php

declare(strict_types=1);

namespace HTMLPurifier\ChildDef;

use HTMLPurifier\Context;
use HTMLPurifier\ChildDef;
use HTMLPurifier\Node;
use HTMLPurifier\Config;

/**
 * Definition that uses different definitions depending on context.
 *
 * The del and ins tags are notable because they allow different types of
 * elements depending on whether or not they're in a block or inline context.
 * Chameleon allows this behavior to happen by using two different
 * definitions depending on context.  While this somewhat generalized,
 * it is specifically intended for those two tags.
 */
class Chameleon extends ChildDef
{
    /**
     * Instance of the definition object to use when inline. Usually stricter.
     *
     * @var Optional
     */
    public $inline;

    /**
     * Instance of the definition object to use when block.
     *
     * @var Optional
     */
    public $block;

    /**
     * @var string
     */
    public $type = 'chameleon';

    /**
     * @param array|string $inline List of elements to allow when inline.
     * @param array|string $block  List of elements to allow when block.
     */
    public function __construct($inline, $block)
    {
        $this->inline = new Optional($inline);
        $this->block = new Optional($block);

        $this->elements = $this->block->elements;
    }

    /**
     * @param Node[]  $children
     * @param Config  $config
     * @param Context $context
     *
     * @return bool|array
     * @throws \HTMLPurifier\Exception
     */
    public function validateChildren(array $children, Config $config, Context $context)
    {
        if ($context->get('IsInline') === false) {
            return $this->block->validateChildren(
                $children,
                $config,
                $context
            );
        }

        return $this->inline->validateChildren(
            $children,
            $config,
            $context
        );
    }
}
