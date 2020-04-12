<?php

declare(strict_types=1);

namespace HTMLPurifier\Token;

use Exception;
use HTMLPurifier\Token;
use HTMLPurifier\Node;
use HTMLPurifier\Token\Tag;

/**
 * Concrete end token class.
 *
 * @warning This class accepts attributes even though end tags cannot. This
 * is for optimization reasons, as under normal circumstances, the Lexers
 * do not pass attributes.
 */
class End extends Tag
{
    /**
     * Token that started this node.
     * Added by MakeWellFormed. Please do not edit this!
     *
     * @type Token
     */
    public $start;

    public function toNode(): Node
    {
        throw new Exception('HTMLPurifier\Token\HTMLPurifier_Token_End->toNode not supported!');
    }
}
