<?php

declare(strict_types=1);

namespace HTMLPurifier\ConfigSchema;

use HTMLPurifier\ConfigSchema\Interchange\Directive;

/**
 * Generic schema interchange format that can be converted to a runtime
 * representation (HTMLPurifier\HTMLPurifier_ConfigSchema) or HTML documentation. Members
 * are completely validated.
 */
class Interchange
{
    /**
     * Name of the application this schema is describing.
     *
     * @type string
     */
    public $name;

    /**
     * Array of Directive ID => array(directive info)
     *
     * @type Directive[]
     */
    public $directives = [];

    /**
     * Adds a directive array to $directives
     *
     * @param Directive $directive
     *
     * @throws Exception
     */
    public function addDirective(Directive $directive): void
    {
        if (isset($this->directives[$i = $directive->id->toString()])) {
            throw new Exception("Cannot redefine directive '{$i}'");
        }

        $this->directives[$i] = $directive;
    }

    /**
     * Convenience function to perform standard validation. Throws exception
     * on failed validation.
     *
     * @throws Exception
     */
    public function validate(): bool
    {
        $validator = new Validator();

        return $validator->validate($this);
    }
}
