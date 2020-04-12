<?php

use HTMLPurifier\Token;
use HTMLPurifier\Token\Start;

class HTMLPurifier_Strategy_MakeWellFormed_EndRewindInjector extends HTMLPurifier_Injector
{
    public $name = 'EndRewindInjector';
    public $needed = array('span');
    public function handleElement(Token &$token)
    {
        if (isset($token->_InjectorTest_EndRewindInjector_delete)) {
            $token = false;
        }
    }
    public function handleText(HTMLPurifier_Token_Text &$token)
    {
        $token = false;
    }
    public function handleEnd(Token &$token)
    {
        $i = null;
        if (
            $this->backward($i, $prev) &&
            $prev instanceof Start &&
            $prev->name == 'span'
        ) {
            $token = false;
            $prev->_InjectorTest_EndRewindInjector_delete = true;
            $this->rewindOffset(1);
        }
    }
}

// vim: et sw=4 sts=4
