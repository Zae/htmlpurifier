<?php

declare(strict_types=1);

use HTMLPurifier\Token;
use HTMLPurifier\Token\Start;

/**
 * Validate all attributes in the tokens.
 */
class HTMLPurifier_Strategy_ValidateAttributes extends HTMLPurifier_Strategy
{
    /**
     * @param Token[]              $tokens
     * @param HTMLPurifier_Config  $config
     * @param HTMLPurifier_Context $context
     *
     * @return Token[]
     */
    public function execute($tokens, HTMLPurifier_Config $config, HTMLPurifier_Context $context): array
    {
        // setup validator
        $validator = new HTMLPurifier_AttrValidator();

        $token = false;
        $context->register('CurrentToken', $token);

        foreach ($tokens as $token) {
            // only process tokens that have attributes,
            //   namely start and empty tags
            if (!$token instanceof Start && !$token instanceof HTMLPurifier_Token_Empty) {
                continue;
            }

            // skip tokens that are armored
            if (!empty($token->armor['ValidateAttributes'])) {
                continue;
            }

            // note that we have no facilities here for removing tokens
            $validator->validateToken($token, $config, $context);
        }

        $context->destroy('CurrentToken');

        return $tokens;
    }
}
