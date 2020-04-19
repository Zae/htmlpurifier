<?php

declare(strict_types=1);

namespace HTMLPurifier\Strategy;

use HTMLPurifier\Context;
use HTMLPurifier\Strategy;
use HTMLPurifier\Token;
use \HTMLPurifier\Config;

/**
 * Composite strategy that runs multiple strategies on tokens.
 */
abstract class Composite extends Strategy
{
    /**
     * List of strategies to run tokens through.
     *
     * @type Strategy[]
     */
    protected $strategies = [];

    /**
     * @param Token[] $tokens
     * @param Config  $config
     * @param Context $context
     *
     * @return Token[]
     */
    public function execute($tokens, Config $config, Context $context)
    {
        foreach ($this->strategies as $strategy) {
            $tokens = $strategy->execute($tokens, $config, $context);
        }

        return $tokens;
    }
}
