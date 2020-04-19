<?php

use HTMLPurifier\Injector;
use HTMLPurifier\Token;
use HTMLPurifier\Token\End;
use HTMLPurifier\Token\Text;
use HTMLPurifier\Token\Start;

class HTMLPurifier_Strategy_MakeWellFormed_EndInsertInjector extends Injector
{
    public $name = 'EndInsertInjector';
    public $needed = array('span');
    public function handleEnd(Token &$token)
    {
        if ($token->name == 'div') return;
        $token = array(
            new Start('b'),
            new Text('Comment'),
            new End('b'),
            $token
        );
    }
}

// vim: et sw=4 sts=4
