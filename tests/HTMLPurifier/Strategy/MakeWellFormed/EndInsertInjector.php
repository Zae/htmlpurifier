<?php

use HTMLPurifier\Token;
use HTMLPurifier\Token\End;
use HTMLPurifier\Token\Start;

class HTMLPurifier_Strategy_MakeWellFormed_EndInsertInjector extends HTMLPurifier_Injector
{
    public $name = 'EndInsertInjector';
    public $needed = array('span');
    public function handleEnd(Token &$token)
    {
        if ($token->name == 'div') return;
        $token = array(
            new Start('b'),
            new HTMLPurifier_Token_Text('Comment'),
            new End('b'),
            $token
        );
    }
}

// vim: et sw=4 sts=4
