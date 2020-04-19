<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Strategy;

use HTMLPurifier\Lexer\DirectLex;
use HTMLPurifier\Strategy;

/**
 * Class ErrorsTestCase
 *
 * @package HTMLPurifier\Tests\Unit\Strategy
 */
abstract class ErrorsTestCase extends \HTMLPurifier\Tests\Unit\ErrorsTestCase
{
    // needs to be defined
    abstract protected function getStrategy(): Strategy;

    /**
     * @param $input
     */
    protected function invoke($input)
    {
        $strategy = $this->getStrategy();
        $lexer = new DirectLex();
        $tokens = $lexer->tokenizeHTML($input, $this->config, $this->context);
        $strategy->execute($tokens, $this->config, $this->context);
    }
}
