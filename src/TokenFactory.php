<?php

declare(strict_types=1);

namespace HTMLPurifier;

use HTMLPurifier\Token\End;
use HTMLPurifier\Token\Start;
use HTMLPurifier_Token_Comment;
use HTMLPurifier_Token_Empty;
use HTMLPurifier_Token_Text;

/**
 * Factory for token generation.
 *
 * @note Doing some benchmarking indicates that the new operator is much
 *       slower than the clone operator (even discounting the cost of the
 *       constructor).  This class is for that optimization.
 *       Other then that, there's not much point as we don't
 *       maintain parallel HTMLPurifier\HTMLPurifier_Token hierarchies (the main reason why
 *       you'd want to use an abstract factory).
 * @todo Port DirectLex to use this
 */
class TokenFactory
{
    // p stands for prototype

    /**
     * @type Start
     */
    private $p_start;

    /**
     * @type End
     */
    private $p_end;

    /**
     * @type HTMLPurifier_Token_Empty
     */
    private $p_empty;

    /**
     * @type HTMLPurifier_Token_Text
     */
    private $p_text;

    /**
     * @type HTMLPurifier_Token_Comment
     */
    private $p_comment;

    /**
     * Generates blank prototypes for cloning.
     */
    public function __construct()
    {
        $this->p_start = new Start('', []);
        $this->p_end = new End('');
        $this->p_empty = new HTMLPurifier_Token_Empty('', []);
        $this->p_text = new HTMLPurifier_Token_Text('');
        $this->p_comment = new HTMLPurifier_Token_Comment('');
    }

    /**
     * Creates a HTMLPurifier\Token\HTMLPurifier_Token_Start.
     *
     * @param string $name Tag name
     * @param array  $attr Associative array of attributes
     *
     * @return Start Generated HTMLPurifier\Token\HTMLPurifier_Token_Start
     */
    public function createStart(string $name, array $attr = []): \HTMLPurifier\Token\Start
    {
        $p = clone $this->p_start;
        $p->__construct($name, $attr);

        return $p;
    }

    /**
     * Creates a HTMLPurifier\Token\HTMLPurifier_Token_End.
     *
     * @param string $name Tag name
     *
     * @return End Generated HTMLPurifier\Token\HTMLPurifier_Token_End
     */
    public function createEnd(string $name): End
    {
        $p = clone $this->p_end;
        $p->__construct($name);

        return $p;
    }

    /**
     * Creates a HTMLPurifier_Token_Empty.
     *
     * @param string $name Tag name
     * @param array  $attr Associative array of attributes
     *
     * @return HTMLPurifier_Token_Empty Generated HTMLPurifier_Token_Empty
     */
    public function createEmpty(string $name, array $attr = []): HTMLPurifier_Token_Empty
    {
        $p = clone $this->p_empty;
        $p->__construct($name, $attr);

        return $p;
    }

    /**
     * Creates a HTMLPurifier_Token_Text.
     *
     * @param string $data Data of text token
     *
     * @return HTMLPurifier_Token_Text Generated HTMLPurifier_Token_Text
     */
    public function createText(string $data): HTMLPurifier_Token_Text
    {
        $p = clone $this->p_text;
        $p->__construct($data);

        return $p;
    }

    /**
     * Creates a HTMLPurifier_Token_Comment.
     *
     * @param string $data Data of comment token
     *
     * @return HTMLPurifier_Token_Comment Generated HTMLPurifier_Token_Comment
     */
    public function createComment(string $data): HTMLPurifier_Token_Comment
    {
        $p = clone $this->p_comment;
        $p->__construct($data);

        return $p;
    }
}
