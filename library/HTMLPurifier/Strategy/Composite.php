<?php

declare(strict_types=1);

use HTMLPurifier\Context;
use HTMLPurifier\Strategy;
use HTMLPurifier\Token;

/**
 * Composite strategy that runs multiple strategies on tokens.
 */
abstract class HTMLPurifier_Strategy_Composite extends Strategy
{
    /**
     * List of strategies to run tokens through.
     *
     * @type Strategy[]
     */
    protected $strategies = [];

    /**
     * @param Token[]             $tokens
     * @param HTMLPurifier_Config $config
     * @param Context             $context
     *
     * @return Token[]
     */
    public function execute($tokens, HTMLPurifier_Config $config, Context $context)
    {
        foreach ($this->strategies as $strategy) {
            $tokens = $strategy->execute($tokens, $config, $context);
        }

        return $tokens;
    }
}
