<?php

declare(strict_types=1);

/**
 * This variable parser uses PHP's internal code engine. Because it does
 * this, it can represent all inputs; however, it is dangerous and cannot
 * be used by users.
 */
class HTMLPurifier_VarParser_Native extends HTMLPurifier_VarParser
{
    /**
     * @param mixed $var
     * @param int   $type
     * @param bool  $allow_null
     *
     * @return null|string
     * @throws HTMLPurifier_VarParserException
     */
    protected function parseImplementation($var, int $type, bool $allow_null)
    {
        return $this->evalExpression($var);
    }

    /**
     * @param string $expr
     *
     * @return mixed
     * @throws HTMLPurifier_VarParserException
     */
    protected function evalExpression($expr)
    {
        $var = null;
        $result = eval("\$var = $expr;");

        if ($result === false) {
            throw new HTMLPurifier_VarParserException('Fatal error in evaluated code');
        }

        return $var;
    }
}
