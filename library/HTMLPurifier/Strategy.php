<?php

declare(strict_types=1);

use HTMLPurifier\Token;

/**
 * Supertype for classes that define a strategy for modifying/purifying tokens.
 *
 * While HTMLPurifier's core purpose is fixing HTML into something proper,
 * strategies provide plug points for extra configuration or even extra
 * features, such as custom tags, custom parsing of text, etc.
 */
abstract class HTMLPurifier_Strategy
{
    /**
     * Executes the strategy on the tokens.
     *
     * @param Token[]              $tokens Array of HTMLPurifier\HTMLPurifier_Token objects to be operated on.
     * @param HTMLPurifier_Config  $config
     * @param HTMLPurifier_Context $context
     *
     * @return Token[] Processed array of token objects.
     */
    abstract public function execute($tokens, HTMLPurifier_Config $config, HTMLPurifier_Context $context);
}
