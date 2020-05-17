<?php

declare(strict_types=1);

namespace HTMLPurifier\Injector;

use HTMLPurifier\Context;
use HTMLPurifier\AttrValidator;
use HTMLPurifier\Injector;
use HTMLPurifier\Token;
use HTMLPurifier\Token\End;
use HTMLPurifier\Token\Start;
use HTMLPurifier\Config;

/**
 * Injector that removes spans with no attributes
 */
class RemoveSpansWithoutAttributes extends Injector
{
    /**
     * @type string
     */
    public $name = 'RemoveSpansWithoutAttributes';

    /**
     * @type array
     */
    public $needed = ['span'];

    /**
     * @type AttrValidator
     */
    private $attrValidator;

    /**
     * Used by AttrValidator.
     *
     * @type Config
     */
    private $config;

    /**
     * @type Context
     */
    private $context;

    public function prepare(Config $config, Context $context)
    {
        $this->attrValidator = new AttrValidator();
        $this->config = $config;
        $this->context = $context;

        return parent::prepare($config, $context);
    }

    /**
     * @param Token $token
     * @param-out Token|false $token
     *
     * @throws \HTMLPurifier\Exception
     * @return void
     */
    public function handleElement(Token &$token)
    {
        if ($token->name !== 'span' || !$token instanceof Start) {
            return;
        }

        // We need to validate the attributes now since this doesn't normally
        // happen until after MakeWellFormed. If all the attributes are removed
        // the span needs to be removed too.
        $this->attrValidator->validateToken($token, $this->config, $this->context);
        $token->armor['ValidateAttributes'] = true;

        if (!empty($token->attr)) {
            return;
        }

        $nesting = 0;
        $current = null;
        while ($this->forwardUntilEndToken($i, $current, $nesting)) {
        }

        if ($current instanceof End && $current->name === 'span') {
            // Mark closing span tag for deletion
            $current->markForDeletion = true;
            // Delete open span tag
            $token = false;
        }
    }

    /**
     * @param Token $token
     * @param-out Token|false $token
     * @return void
     */
    public function handleEnd(Token &$token)
    {
        if ($token->markForDeletion) {
            $token = false;
        }
    }
}
