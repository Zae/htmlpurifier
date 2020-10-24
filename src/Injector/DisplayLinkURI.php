<?php

declare(strict_types=1);

namespace HTMLPurifier\Injector;

use HTMLPurifier\Injector;
use HTMLPurifier\Token;
use HTMLPurifier\Token\End;
use HTMLPurifier\Token\Start;
use HTMLPurifier\Token\Tag;
use HTMLPurifier\Token\Text;

/**
 * Injector that displays the URL of an anchor instead of linking to it, in addition to showing the text of the link.
 */
class DisplayLinkURI extends Injector
{
    /**
     * @var string
     */
    public $name = 'DisplayLinkURI';

    /**
     * @var array
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
     * @param End $token
     *
     * @return void
     *
     * @param-out End|array{End, Text} $token
     */
    public function handleEnd(End &$token): void
    {
        if ($token->start instanceof Tag && isset($token->start->attr['href'])) {
            $url = $token->start->attr['href'];
            unset($token->start->attr['href']);
            $token = [$token, new Text(" ($url)")];
        }
    }
}
