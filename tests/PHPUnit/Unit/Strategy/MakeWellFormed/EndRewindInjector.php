<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Strategy\MakeWellFormed;

use HTMLPurifier\Injector;
use HTMLPurifier\Token;
use HTMLPurifier\Token\Start;
use HTMLPurifier\Token\Text;

/**
 * Class EndRewindInjector
 *
 * @package HTMLPurifier\Tests\Unit\Strategy\MakeWellFormed
 */
class EndRewindInjector extends Injector
{
    public $name = 'EndRewindInjector';
    public $needed = ['span'];

    /**
     * Handler that is called when a start or empty token is processed
     *
     * @param Token $token
     * @return void
     */
    public function handleElement(Token &$token)
    {
        if (isset($token->_InjectorTest_EndRewindInjector_delete)) {
            $token = false;
        }
    }

    /**
     * Handler that is called when a text token is processed
     *
     * @param Text $token
     * @return void
     */
    public function handleText(Text &$token)
    {
        $token = false;
    }

    /**
     * Handler that is called when an end token is processed
     *
     * @param Token $token
     * @return void
     */
    public function handleEnd(Token &$token)
    {
        $i = null;
        if (
            $this->backward($i, $prev) &&
            $prev instanceof Start &&
            $prev->name === 'span'
        ) {
            $token = false;
            $prev->_InjectorTest_EndRewindInjector_delete = true;
            $this->rewindOffset(1);
        }
    }
}
