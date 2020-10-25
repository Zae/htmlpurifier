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
     * @var string
     */
    public $name = 'RemoveSpansWithoutAttributes';

    /**
     * @var array
     */
    public $needed = ['span'];

    /**
     * @var AttrValidator|null
     */
    private $attrValidator;

    /**
     * Used by AttrValidator.
     *
     * @var Config|null
     */
    private $config;

    /**
     * @var Context|null
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
     *
     * @return void
     * @throws \HTMLPurifier\Exception
     *
     * @param-out Token|false $token
     *
     */
    public function handleElement(Token &$token): void
    {
        if ($token->name !== 'span' || !$token instanceof Start) {
            return;
        }

        if ($this->attrValidator === null || $this->config === null || $this->context === null) {
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
     * @param End $token
     *
     * @return void
     *
     * @param-out Token|false $token
     */
    public function handleEnd(End &$token): void
    {
        if ($token->markForDeletion) {
            $token = false;
        }
    }
}
