<?php

declare(strict_types=1);

namespace HTMLPurifier\Injector;

use HTMLPurifier\Injector;
use HTMLPurifier\Token;
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
            $token = [$token, new Text(" ($url)")];
        } else {
            // nothing to display
        }
    }
}
