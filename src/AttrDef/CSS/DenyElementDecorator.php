<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\CSS;

use HTMLPurifier\AttrDef;
use HTMLPurifier\Config;
use HTMLPurifier\Context;

use function is_null;

/**
 * Decorator which enables CSS properties to be disabled for specific elements.
 */
class DenyElementDecorator extends AttrDef
{
    /**
     * @var AttrDef
     */
    public $def;

    /**
     * @var string
     */
    public $element;

    /**
     * @param AttrDef $def     Definition to wrap
     * @param string  $element Element to deny
     */
    public function __construct(AttrDef $def, string $element)
    {
        $this->def = $def;
        $this->element = $element;
    }

    /**
     * Checks if CurrentToken is set and equal to $this->element
     *
     * @param string       $string
     * @param Config|null  $config
     * @param Context|null $context
     *
     * @return bool|null|string
     * @throws \HTMLPurifier\Exception
     */
    public function validate(string $string, ?Config $config, ?Context $context)
    {
        if (is_null($context)) {
            return false;
        }

        $token = $context->get('CurrentToken', true);
        if ($token && $token->name === $this->element) {
            return false;
        }

        return $this->def->validate($string, $config, $context);
    }
}
