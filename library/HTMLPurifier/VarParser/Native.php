<?php

declare(strict_types=1);

use HTMLPurifier\VarParserException;
use HTMLPurifier\VarParser;

/**
 * This variable parser uses PHP's internal code engine. Because it does
 * this, it can represent all inputs; however, it is dangerous and cannot
 * be used by users.
 */
class HTMLPurifier_VarParser_Native extends VarParser
{
    /**
     * @param mixed $var
     * @param int   $type
     * @param bool  $allow_null
     *
     * @return null|string
     * @throws VarParserException
     */
    protected function parseImplementation($var, int $type, bool $allow_null)
    {
        return $this->evalExpression($var);
    }

    /**
     * @param string $expr
     *
     * @return mixed
     * @throws VarParserException
     */
    protected function evalExpression($expr)
    {
        $var = null;
        $result = eval("\$var = $expr;");

        if ($result === false) {
            throw new VarParserException('Fatal error in evaluated code');
        }

        return $var;
    }
}
