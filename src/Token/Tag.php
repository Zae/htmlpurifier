<?php

declare(strict_types=1);

namespace HTMLPurifier\Token;

use HTMLPurifier\Token;
use HTMLPurifier\Node;
use HTMLPurifier\Node\Element;

/**
 * Abstract class of a tag token (start, end or empty), and its behavior.
 */
abstract class Tag extends Token
{
    /**
     * Static bool marker that indicates the class is a tag.
     *
     * This allows us to check objects with <tt>!empty($obj->is_tag)</tt>
     * without having to use a function call <tt>is_a()</tt>.
     *
     * @var bool
     */
    public $is_tag = true;

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
     * Associative array of the tag's attributes.
     *
     * @var array
     */
    public $attr = [];

    /**
     * Non-overloaded constructor, which lower-cases passed tag name.
     *
     * @param string    $name String name.
     * @param array     $attr Associative array of attributes.
     * @param int|null  $line
     * @param int|null  $col
     * @param array     $armor
     */
    public function __construct(string $name, array $attr = [], ?int $line = null, ?int $col = null, array $armor = [])
    {
        $this->name = ctype_lower($name) ? $name : strtolower($name);

        foreach ($attr as $key => $value) {
            // normalization only necessary when key is not lowercase
            if (!ctype_lower($key)) {
                $new_key = strtolower($key);
                if (!isset($attr[$new_key])) {
                    $attr[$new_key] = $attr[$key];
                }

                if ($new_key !== $key) {
                    unset($attr[$key]);
                }
            }
        }

        $this->attr = $attr;
        $this->line = $line;
        $this->col = $col;
        $this->armor = $armor;
    }

    public function toNode(): Node
    {
        return new Element($this->name, $this->attr, $this->line, $this->col, $this->armor);
    }
}
