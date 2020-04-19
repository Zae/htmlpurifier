<?php

declare(strict_types=1);

namespace HTMLPurifier\Token;

use HTMLPurifier\Token;
use HTMLPurifier\Node;

/**
 * Concrete comment token class. Generally will be ignored.
 */
class Comment extends Token
{
    /**
     * Character data within comment.
     *
     * @type string
     */
    public $data;

    /**
     * @type bool
     */
    public $is_whitespace = true;

    /**
     * Transparent constructor.
     *
     * @param string $data String comment data.
     * @param int    $line
     * @param int    $col
     */
    public function __construct(string $data, ?int $line = null, ?int $col = null)
    {
        $this->data = $data;
        $this->line = $line;
        $this->col = $col;
    }

    /**
     * Converts a token into its corresponding node.
     *
     * @return Node
     */
    public function toNode(): Node
    {
        return new Node\Comment($this->data, $this->line, $this->col);
    }
}
