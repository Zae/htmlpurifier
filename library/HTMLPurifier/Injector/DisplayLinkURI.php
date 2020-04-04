<?php

declare(strict_types=1);

/**
 * Injector that displays the URL of an anchor instead of linking to it, in addition to showing the text of the link.
 */
class HTMLPurifier_Injector_DisplayLinkURI extends HTMLPurifier_Injector
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
     * @param HTMLPurifier_Token $token
     */
    public function handleElement(HTMLPurifier_Token &$token)
    {
    }

    /**
     * @param HTMLPurifier_Token $token
     */
    public function handleEnd(HTMLPurifier_Token &$token)
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
