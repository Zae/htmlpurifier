<?php

declare(strict_types=1);

namespace HTMLPurifier\Token;

use HTMLPurifier\Token;
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
class Text extends Token
{
    /**
     * @type string
     */
    public $name = '#PCDATA';
    /**< PCDATA tag name compatible with DTD. */

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
     * @param int    $line
     * @param int    $col
     */
    public function __construct(string $data, ?int $line = null, ?int $col = null)
    {
        $this->data = $data;
        $this->is_whitespace = ctype_space($data);
        $this->line = $line;
        $this->col = $col;
    }

    public function toNode(): Node
    {
        return new Node\Text($this->data, $this->is_whitespace, $this->line, $this->col);
    }
}
