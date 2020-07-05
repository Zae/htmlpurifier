<?php

declare(strict_types=1);

namespace HTMLPurifier\ConfigSchema\Interchange;

/**
 * Interchange component class describing configuration directives.
 */
class Directive
{
    /**
     * ID of directive.
     *
     * @var Id|null
     */
    public $id;

    /**
     * Type, e.g. 'integer' or 'istring'.
     *
     * @var string
     */
    public $type = '';

    /**
     * Default value, e.g. 3 or 'DefaultVal'.
     *
     * @var mixed
     */
    public $default;

    /**
     * HTML description.
     *
     * @var string
     */
    public $description = '';

    /**
     * Whether or not null is allowed as a value.
     *
     * @var bool
     */
    public $typeAllowsNull = false;

    /**
     * Lookup table of allowed scalar values.
     * e.g. array('allowed' => true).
     * Null if all values are allowed.
     *
     * @var array|null
     */
    public $allowed;

    /**
     * List of aliases for the directive.
     * e.g. array(new HTMLPurifier_ConfigSchema_Interchange_Id('Ns', 'Dir'))).
     *
     * @var Id[]
     */
    public $aliases = [];

    /**
     * Hash of value aliases, e.g. array('alt' => 'real'). Null if value
     * aliasing is disabled (necessary for non-scalar types).
     *
     * @var array|null
     */
    public $valueAliases;

    /**
     * Version of HTML Purifier the directive was introduced, e.g. '1.3.1'.
     * Null if the directive has always existed.
     *
     * @var string|null
     */
    public $version;

    /**
     * ID of directive that supercedes this old directive.
     * Null if not deprecated.
     *
     * @var Id|null
     */
    public $deprecatedUse;

    /**
     * Version of HTML Purifier this directive was deprecated. Null if not
     * deprecated.
     *
     * @var string|null
     */
    public $deprecatedVersion;

    /**
     * List of external projects this directive depends on, e.g. array('CSSTidy').
     *
     * @var array
     */
    public $external = [];
}
