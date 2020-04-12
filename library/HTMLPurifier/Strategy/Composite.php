<?php

declare(strict_types=1);

use HTMLPurifier\Token;

/**
 * Composite strategy that runs multiple strategies on tokens.
 */
abstract class HTMLPurifier_Strategy_Composite extends HTMLPurifier_Strategy
{
    /**
     * List of strategies to run tokens through.
     *
     * @type HTMLPurifier_Strategy[]
     */
    protected $strategies = [];

    /**
     * @param Token[]              $tokens
     * @param HTMLPurifier_Config  $config
     * @param HTMLPurifier_Context $context
     *
     * @return Token[]
     */
    public function execute($tokens, HTMLPurifier_Config $config, HTMLPurifier_Context $context)
    {
        foreach ($this->strategies as $strategy) {
            $tokens = $strategy->execute($tokens, $config, $context);
        }

        return $tokens;
    }
}
