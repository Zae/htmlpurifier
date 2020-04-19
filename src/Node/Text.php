<?php

declare(strict_types=1);

namespace HTMLPurifier\Node;

use HTMLPurifier\Node;

/**
 * Concrete text token class.
 *
 * Text tokens comprise of regular parsed character data (PCDATA) and raw
 * character data (from the CDATA sections). Internally, their
 * data is parsed with all entities expanded. Surprisingly, the text token
 * does have a "tag name" called #PCDATA, which is how the DTD represents it
 * in permissible child nodes.
 */
class Text extends Node
{
    /**
     * PCDATA tag name compatible with DTD, see
     * HTMLPurifier\ChildDef\HTMLPurifier_ChildDef_Custom for details.
     *
     * @type string
     */
    public $name = '#PCDATA';

    /**
     * @type string
     */
    public $data;
    /**< Parsed character data of text. */

    /**
     * @type bool
     */
    public $is_whitespace;

    /**< Bool indicating if node is whitespace. */

    /**
     * Constructor, accepts data and determines if it is whitespace.
     *
     * @param string $data String parsed character data.
     * @param bool   $is_whitespace
     * @param int    $line
     * @param int    $col
     */
    public function __construct(string $data, bool $is_whitespace, ?int $line = null, ?int $col = null)
    {
        $this->data = $data;
        $this->is_whitespace = $is_whitespace;
        $this->line = $line;
        $this->col = $col;
    }

    /**
     * Returns a pair of start and end tokens, where the end token
     * is null if it is not necessary. Does not include children.
     *
     * @type array
     * @return array
     */
    public function toTokenPair(): array
    {
        return [new \HTMLPurifier\Token\Text($this->data, $this->line, $this->col), null];
    }
}
