<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Strategy\MakeWellFormed;

use HTMLPurifier\Injector;
use HTMLPurifier\Token;
use HTMLPurifier\Token\End;
use HTMLPurifier\Token\Start;
use HTMLPurifier\Token\Text;

/**
 * Class EndInsertInjector
 *
 * @package HTMLPurifier\Tests\Unit\Strategy\MakeWellFormed
 */
class EndInsertInjector extends Injector
{
    public $name = 'EndInsertInjector';
    public $needed = ['span'];

    /**
     * Handler that is called when an end token is processed
     *
     * @param End $token
     *
     * @return void
     */
    public function handleEnd(End &$token): void
    {
        if ($token->name === 'div') {
            return;
        }

        $token = [
            new Start('b'),
            new Text('Comment'),
            new End('b'),
            $token
        ];
    }
}
