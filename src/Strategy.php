<?php

declare(strict_types=1);

namespace HTMLPurifier;

/**
 * Supertype for classes that define a strategy for modifying/purifying tokens.
 *
 * While HTMLPurifier's core purpose is fixing HTML into something proper,
 * strategies provide plug points for extra configuration or even extra
 * features, such as custom tags, custom parsing of text, etc.
 */
abstract class Strategy
{
    /**
     * Executes the strategy on the tokens.
     *
     * @param Token[] $tokens Array of HTMLPurifier\HTMLPurifier_Token objects to be operated on.
     * @param Config  $config
     * @param Context $context
     *
     * @return Token[] Processed array of token objects.
     */
    abstract public function execute($tokens, Config $config, Context $context);
}
