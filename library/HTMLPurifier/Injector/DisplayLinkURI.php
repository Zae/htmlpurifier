<?php

declare(strict_types=1);

use HTMLPurifier\Injector;
use HTMLPurifier\Token;

/**
 * Injector that displays the URL of an anchor instead of linking to it, in addition to showing the text of the link.
 */
class HTMLPurifier_Injector_DisplayLinkURI extends Injector
{
    /**
     * @type string
     */
    public $name = 'DisplayLinkURI';

    /**
     * @type array
     */
    public $needed = array('a');

    /**
     * @param Token $token
     */
    public function handleElement(Token &$token)
    {
    }

    /**
     * @param Token $token
     */
    public function handleEnd(Token &$token)
    {
        if (isset($token->start->attr['href'])) {
            $url = $token->start->attr['href'];
            unset($token->start->attr['href']);
            $token = [$token, new HTMLPurifier_Token_Text(" ($url)")];
        } else {
            // nothing to display
        }
    }
}
