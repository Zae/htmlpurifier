<?php

declare(strict_types=1);

namespace HTMLPurifier;

/**
 * Abstract base node class that all others inherit from.
 *
 * Why do we not use the DOM extension?  (1) It is not always available,
 * (2) it has funny constraints on the data it can represent,
 * whereas we want a maximally flexible representation, and (3) its
 * interface is a bit cumbersome.
 */
abstract class Node
{
    /**
     * Line number of the start token in the source document
     *
     * @var int|null
     */
    public $line;

    /**
     * Column number of the start token in the source document. Null if unknown.
     *
     * @var int|null
     */
    public $col;

    /**
     * Lookup array of processing that this token is exempt from.
     * Currently, valid values are "ValidateAttributes".
     *
     * @var array
     */
    public $armor = [];

    /**
     * When true, this node should be ignored as non-existent.
     *
     * Who is responsible for ignoring dead nodes?  FixNesting is
     * responsible for removing them before passing on to child
     * validators.
     */
    public $dead = false;

    /**
     * Does this use the <a></a> form or the </a> form, i.e.
     * is it a pair of start/end tokens or an empty token.
     *
     * @bool
     */
    public $empty = false;

    /**
     * @var bool
     */
    public $is_whitespace = false;

    /**
     * PCDATA tag name compatible with DTD, see
     * HTMLPurifier\ChildDef\HTMLPurifier_ChildDef_Custom for details.
     *
     * @var string
     */
    public $name = '';

    /**
     * Returns a pair of start and end tokens, where the end token
     * is null if it is not necessary. Does not include children.
     *
     * @return array
     */
    abstract public function toTokenPair();
}
