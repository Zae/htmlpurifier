<?php

declare(strict_types=1);

namespace HTMLPurifier;

/**
 * Represents a document type, contains information on which modules
 * need to be loaded.
 *
 * @note This class is inspected by Printer_HTMLDefinition->renderDoctype.
 *       If structure changes, please update that function.
 */
class Doctype
{
    /**
     * Full name of doctype
     *
     * @var string
     */
    public $name = '';

    /**
     * List of standard modules (string identifiers or literal objects)
     * that this doctype uses
     *
     * @var array
     */
    public $modules = [];

    /**
     * List of modules to use for tidying up code
     *
     * @var array
     */
    public $tidyModules = [];

    /**
     * Is the language derived from XML (i.e. XHTML)?
     *
     * @var bool
     */
    public $xml = true;

    /**
     * List of aliases for this doctype
     *
     * @var array
     */
    public $aliases = [];

    /**
     * Public DTD identifier
     *
     * @var string|null
     */
    public $dtdPublic;

    /**
     * System DTD identifier
     *
     * @var string|null
     */
    public $dtdSystem;

    /**
     * HTMLPurifier\HTMLPurifier_Doctype constructor.
     *
     * @param string        $name
     * @param bool          $xml
     * @param array         $modules
     * @param array         $tidyModules
     * @param array         $aliases
     * @param string|null   $dtd_public
     * @param string|null   $dtd_system
     */
    public function __construct(
        string $name,
        bool $xml = true,
        array $modules = [],
        array $tidyModules = [],
        array $aliases = [],
        ?string $dtd_public = null,
        ?string $dtd_system = null
    )
    {
        $this->name = $name;
        $this->xml = $xml;
        $this->modules = $modules;
        $this->tidyModules = $tidyModules;
        $this->aliases = $aliases;
        $this->dtdPublic = $dtd_public;
        $this->dtdSystem = $dtd_system;
    }
}
