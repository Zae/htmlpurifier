<?php

declare(strict_types=1);

namespace HTMLPurifier\Node;

use HTMLPurifier\Node;
use HTMLPurifier\Token\EmptyToken;
use HTMLPurifier\Token\End;
use HTMLPurifier\Token\Start;

/**
 * Concrete element node class.
 */
class Element extends Node
{
    /**
     * The lower-case name of the tag, like 'a', 'b' or 'blockquote'.
     *
     * @note Strictly speaking, XML tags are case sensitive, so we shouldn't
     * be lower-casing them, but these tokens cater to HTML tags, which are
     * insensitive.
     * @var string
     */
    public $name;

    /**
     * Associative array of the node's attributes.
     *
     * @var array
     */
    public $attr = [];

    /**
     * List of child elements.
     *
     * @var array
     */
    public $children = [];

    /**
     * @var int|null
     */
    public $endCol = null;

    /**
     * @var int|null
     */
    public $endLine = null;

    /**
     * @var array
     */
    public $endArmor = [];

    /**
     * HTMLPurifier\Node\HTMLPurifier_Node_Element constructor.
     *
     * @param          $name
     * @param array    $attr
     * @param int|null $line
     * @param int|null $col
     * @param array    $armor
     */
    public function __construct(string $name, array $attr = [], ?int $line = null, ?int $col = null, array $armor = [])
    {
        $this->name = $name;
        $this->attr = $attr;
        $this->line = $line;
        $this->col = $col;
        $this->armor = $armor;
    }

    /**
     * Returns a pair of start and end tokens, where the end token
     * is null if it is not necessary. Does not include children.
     *
     * @var array
     * @return array
     */
    public function toTokenPair(): array
    {
        // XXX inefficiency here, normalization is not necessary
        if ($this->empty) {
            return [new EmptyToken($this->name, $this->attr, $this->line, $this->col, $this->armor), null];
        }

        $start = new Start($this->name, $this->attr, $this->line, $this->col, $this->armor);
        $end = new End($this->name, [], $this->endLine, $this->endCol, $this->endArmor);

        //$end->start = $start;
        return [$start, $end];
    }
}

