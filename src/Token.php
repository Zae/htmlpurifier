<?php

declare(strict_types=1);

namespace HTMLPurifier;

use HTMLPurifier\Token\Comment;
use HTMLPurifier\Token\Text;
use HTMLPurifier\Token\End;
use HTMLPurifier\Token\EmptyToken;
use HTMLPurifier\Token\Start;

/**
 * Abstract base token class that all others inherit from.
 */
abstract class Token
{
    /**
     * Line number node was on in source document. Null if unknown.
     *
     * @var int|null
     */
    public $line;

    /**
     * Column of line node was on in source document. Null if unknown.
     *
     * @var int|null
     */
    public $col;

    /**
     * Lookup array of processing that this token is exempt from.
     * Currently, valid values are "ValidateAttributes" and
     * "MakeWellFormed_TagClosedError"
     *
     * @var array
     */
    public $armor = [];

    /**
     * Used during MakeWellFormed.  See Note [Injector skips]
     *
     * @var array|null
     */
    public $skip;

    /**
     * @var mixed|null
     */
    public $rewind;

    /**
     * @var bool|null
     */
    public $carryover;

    /**
     * @var string
     */
    public $name = '';

    /**
     * @param string $n
     *
     * @return null|string
     */
    public function __get(string $n)
    {
        if ($n === 'type') {
            Log::notice('Deprecated type property called; use instanceof');

            switch (\get_class($this)) {
                case Start::class:
                    return 'start';
                case EmptyToken::class:
                    return 'empty';
                case End::class:
                    return 'end';
                case Text::class:
                    return 'text';
                case Comment::class:
                    return 'comment';
                default:
                    return null;
            }
        }

        return null;
    }

    /**
     * Sets the position of the token in the source document.
     *
     * @param int|null $l
     * @param int|null $c
     */
    public function position(?int $l = null, ?int $c = null): void
    {
        $this->line = $l;
        $this->col = $c;
    }

    /**
     * Convenience function for DirectLex settings line/col position.
     *
     * @param int $l
     * @param int $c
     */
    public function rawPosition(int $l, int $c): void
    {
        if ($c === -1) {
            $l++;
        }

        $this->line = $l;
        $this->col = $c;
    }

    /**
     * Converts a token into its corresponding node.
     *
     * @return Node
     */
    abstract public function toNode(): Node;
}
