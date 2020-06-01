<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Strategy\MakeWellFormed;

use HTMLPurifier\Injector;
use HTMLPurifier\Token;

/**
 * Class SkipInjector
 *
 * @package HTMLPurifier\Tests\Unit\Strategy\MakeWellFormed
 */
class SkipInjector extends Injector
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
        $token = [clone $token, clone $token];
    }
}
