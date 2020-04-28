<?php

declare(strict_types=1);

namespace HTMLPurifier\Token;

use HTMLPurifier\Exception;
use HTMLPurifier\Token;
use HTMLPurifier\Node;

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
     * @var Token|null
     */
    public $start;

    /**
     * @var bool|string|null
     */
    public $markForDeletion;

    /**
     * @return Node
     * @throws Exception
     */
    public function toNode(): Node
    {
        throw new Exception('HTMLPurifier\Token\HTMLPurifier_Token_End->toNode not supported!');
    }
}
