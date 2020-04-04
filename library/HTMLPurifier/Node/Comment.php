<?php

declare(strict_types=1);

/**
 * Concrete comment node class.
 */
class HTMLPurifier_Node_Comment extends HTMLPurifier_Node
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
     * Returns a pair of start and end tokens, where the end token
     * is null if it is not necessary. Does not include children.
     *
     * @type array
     * @return array
     */
    public function toTokenPair(): array
    {
        return [new HTMLPurifier_Token_Comment($this->data, $this->line, $this->col), null];
    }
}
