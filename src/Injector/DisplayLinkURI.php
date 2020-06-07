<?php

declare(strict_types=1);

namespace HTMLPurifier\Injector;

use HTMLPurifier\Injector;
use HTMLPurifier\Token;
use HTMLPurifier\Token\End;
use HTMLPurifier\Token\Text;

/**
 * Injector that displays the URL of an anchor instead of linking to it, in addition to showing the text of the link.
 */
class DisplayLinkURI extends Injector
{
    /**
     * @type string
     */
    public $name = 'DisplayLinkURI';

    /**
     * @type array
     */
    public $needed = ['a'];

    /**
     * @param Token $token
     *
     * @return void
     */
    public function handleElement(Token &$token): void
    {
    }

    /**
     * @param Token $token
     *
     * @return void
     *
     * @param-out Token|array{0: End, 1:Text} $token
     */
    public function handleEnd(Token &$token):void
    {
        /**
         * @psalm-suppress InvalidArrayOffset No idea why psalm doesnt like this, TODO FIX.
         */
        if ($token instanceof End && isset($token->start->attr['href'])) {
            $url = $token->start->attr['href'];
            unset($token->start->attr['href']);
            $token = [$token, new Text(" ($url)")];
        } else {
            // nothing to display
        }
    }
}
