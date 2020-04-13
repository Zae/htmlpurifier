<?php

use HTMLPurifier\Injector;
use HTMLPurifier\Token;

class HTMLPurifier_Strategy_MakeWellFormed_SkipInjector extends Injector
{
    public $name = 'EndRewindInjector';
    public $needed = array('span');
    public function handleElement(Token &$token)
    {
        $token = array(clone $token, clone $token);
    }
}

// vim: et sw=4 sts=4
