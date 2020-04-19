<?php

declare(strict_types=1);

namespace HTMLPurifier\Strategy;

use HTMLPurifier\Context;
use HTMLPurifier\AttrValidator;
use HTMLPurifier\Strategy;
use HTMLPurifier\Token;
use HTMLPurifier\Token\Start;
use \HTMLPurifier\Config;
use HTMLPurifier\Token\EmptyToken;

/**
 * Validate all attributes in the tokens.
 */
class ValidateAttributes extends Strategy
{
    /**
     * @param Token[]             $tokens
     * @param \HTMLPurifier\Config $config
     * @param Context             $context
     *
     * @return Token[]
     */
    public function execute($tokens, \HTMLPurifier\Config $config, Context $context): array
    {
        // setup validator
        $validator = new AttrValidator();

        $token = false;
        $context->register('CurrentToken', $token);

        foreach ($tokens as $token) {
            // only process tokens that have attributes,
            //   namely start and empty tags
            if (!$token instanceof Start && !$token instanceof EmptyToken) {
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
