<?php

declare(strict_types=1);

namespace HTMLPurifier;

use function is_array;
use function is_object;
use function is_string;

/**
 * Class HTMLModuleManager
 *
 * @package HTMLPurifier
 */
class HTMLModuleManager
{
    /**
     * @var DoctypeRegistry
     */
    public $doctypes;

    /**
     * Instance of current doctype.
     *
     * @var Doctype|null
     */
    public $doctype;

    /**
     * @var AttrTypes
     */
    public $attrTypes;

    /**
     * Active instances of modules for the specified doctype are
     * indexed, by name, in this array.
     *
     * @var HTMLModule[]
     */
    public $modules = [];

    /**
     * Array of recognized HTMLPurifier\HTMLPurifier_HTMLModule instances,
     * indexed by module's class name. This array is usually lazy loaded, but a
     * user can overload a module by pre-emptively registering it.
     *
     * @var HTMLModule[]
     */
    public $registeredModules = [];

    /**
     * List of extra modules that were added by the user
     * using addModule(). These get unconditionally merged into the current doctype, whatever
     * it may be.
     *
     * @var string[]
     */
    public $userModules = [];

    /**
     * Associative array of element name to list of modules that have
     * definitions for the element; this array is dynamically filled.
     *
     * @var array
     */
    public $elementLookup = [];

    /**
     * List of prefixes we should use for registering small names.
     *
     * @var array
     */
    public $prefixes = [
        'HTMLPurifier\\HTMLModule\\',
    ];

    /**
     * @var ContentSets
     */
    public $contentSets;

    /**
     * @var AttrCollections
     */
    public $attrCollections;

    /**
     * If set to true, unsafe elements and attributes will be allowed.
     *
     * @var bool
     */
    public $trusted = false;

    public function __construct()
    {
        // editable internal objects
        $this->attrTypes = new AttrTypes();
        $this->doctypes = new DoctypeRegistry();
        $this->contentSets = new ContentSets([]);
        $this->attrCollections = new AttrCollections(
            $this->attrTypes,
            []
        );

        // setup basic modules
        $common = [
            'CommonAttributes', 'Text', 'Hypertext', 'Lists',
            'Presentation', 'Edit', 'Bdo', 'Tables', 'Image',
            'StyleAttribute',
            // Unsafe:
            'Scripting', 'Objects', 'Forms',
            // Sorta legacy, but present in strict:
            'Name',
        ];

        $transitional = ['Legacy', 'Target', 'Iframe'];
        $xml = ['XMLCommonAttributes'];
        $non_xml = ['NonXMLCommonAttributes'];

        // setup basic doctypes
        $this->doctypes->register(
            'HTML 4.01 Transitional',
            false,
            array_merge($common, $transitional, $non_xml),
            ['Tidy\\Transitional', 'Tidy\\Proprietary'],
            [],
            '-//W3C//DTD HTML 4.01 Transitional//EN',
            'http://www.w3.org/TR/html4/loose.dtd'
        );

        $this->doctypes->register(
            'HTML 4.01 Strict',
            false,
            array_merge($common, $non_xml),
            ['Tidy\\Strict', 'Tidy\\Proprietary', 'Tidy\\Name'],
            [],
            '-//W3C//DTD HTML 4.01//EN',
            'http://www.w3.org/TR/html4/strict.dtd'
        );

        $this->doctypes->register(
            'XHTML 1.0 Transitional',
            true,
            array_merge($common, $transitional, $xml, $non_xml),
            ['Tidy\\Transitional', 'Tidy\\XHTML', 'Tidy\\Proprietary', 'Tidy\\Name'],
            [],
            '-//W3C//DTD XHTML 1.0 Transitional//EN',
            'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'
        );

        $this->doctypes->register(
            'XHTML 1.0 Strict',
            true,
            array_merge($common, $xml, $non_xml),
            ['Tidy\\Strict', 'Tidy\\XHTML', 'Tidy\\Strict', 'Tidy\\Proprietary', 'Tidy\\Name'],
            [],
            '-//W3C//DTD XHTML 1.0 Strict//EN',
            'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'
        );

        $this->doctypes->register(
            'XHTML 1.1',
            true,
            // Iframe is a real XHTML 1.1 module, despite being
            // "transitional"!
            array_merge($common, $xml, ['Ruby', 'Iframe']),
            ['Tidy\\Strict', 'Tidy\\XHTML', 'Tidy\\Proprietary', 'Tidy\\Strict', 'Tidy\\Name'], // Tidy_XHTML1_1
            [],
            '-//W3C//DTD XHTML 1.1//EN',
            'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd'
        );
    }

    /**
     * Registers a module to the recognized module list, useful for
     * overloading pre-existing modules.
     *
     * @param string|HTMLModule $module   Mixed: string module name, with or without
     *                                    HTMLPurifier\HTMLPurifier_HTMLModule prefix, or instance of
     *                                    subclass of HTMLPurifier\HTMLPurifier_HTMLModule.
     * @param bool              $overload Boolean whether or not to overload previous modules.
     *                                    If this is not set, and you do overload a module,
     *                                    HTML Purifier will complain with a warning.
     *
     * @note This function will not call autoload, you must instantiate
     *       (and thus invoke) autoload outside the method.
     * @note If a string is passed as a module name, different variants
     *       will be tested in this order:
     *          - Check for HTMLPurifier_HTMLModule_$name
     *          - Check for HTMLPurifier\\HTMLModule\\$name
     *          - Check all prefixes with $name in order they were added
     *          - Check for literal object name
     *          - Throw fatal error
     *       If your object name collides with an internal class, specify
     *       your module manually. All modules must have been included
     *       externally: registerModule will not perform inclusions for you!
     * @throws Exception
     */
    public function registerModule($module, bool $overload = false): void
    {
        if (is_string($module)) {
            // attempt to load the module
            $original_module = $module;
            $ok = false;
            foreach ($this->prefixes as $prefix) {
                $module = $prefix . $original_module;

                if (class_exists($module)) {
                    $ok = true;
                    break;
                }
            }

            if (!$ok) {
                $module = $original_module;
                if (!class_exists($module)) {
                    throw new Exception("{$original_module} module does not exist");
                }
            }

            $module = new $module();
        }

        if (empty($module->name)) {
            trigger_error('Module instance of ' . \get_class($module) . ' must have name');

            return;
        }

        if (!$overload && isset($this->registeredModules[$module->name])) {
            trigger_error('Overloading ' . $module->name . ' without explicit overload parameter', E_USER_WARNING);
        }

        if (!$module instanceof HTMLModule) {
            throw new Exception('$module is not a HTMLModule');
        }

        $this->registeredModules[$module->name] = $module;
    }

    /**
     * Adds a module to the current doctype by first registering it,
     * and then tacking it on to the active doctype
     *
     * @param string|HTMLModule $module
     * @throws Exception
     */
    public function addModule($module): void
    {
        $this->registerModule($module);

        if (is_object($module)) {
            $module = $module->name;
        }

        $this->userModules[] = $module;
    }

    /**
     * Adds a class prefix that registerModule() will use to resolve a
     * string name to a concrete class
     *
     * @param string $prefix
     */
    public function addPrefix(string $prefix): void
    {
        $this->prefixes[] = $prefix;
    }

    /**
     * Performs processing on modules, after being called you may
     * use getElement() and getElements()
     *
     * @param Config $config
     *
     * @throws Exception
     */
    public function setup(Config $config): void
    {
        $this->trusted = $config->get('HTML.Trusted');

        // generate
        $this->doctype = $this->doctypes->make($config);
        $modules = $this->doctype->modules;


        // take out the default modules that aren't allowed
        $lookup = $config->get('HTML.AllowedModules');
        $special_cases = $config->get('HTML.CoreModules');

        if (is_array($lookup)) {
            foreach ($modules as $k => $m) {
                if (isset($special_cases[$m])) {
                    continue;
                }
                if (!isset($lookup[$m])) {
                    unset($modules[$k]);
                }
            }
        }

        // custom modules
        if ($config->get('HTML.Proprietary')) {
            $modules[] = 'Proprietary';
        }

        if ($config->get('HTML.SafeObject')) {
            $modules[] = 'SafeObject';
        }

        if ($config->get('HTML.SafeEmbed')) {
            $modules[] = 'SafeEmbed';
        }

        if ($config->get('HTML.SafeScripting') !== []) {
            $modules[] = 'SafeScripting';
        }

        if ($config->get('HTML.Nofollow')) {
            $modules[] = 'Nofollow';
        }

        if ($config->get('HTML.TargetBlank')) {
            $modules[] = 'TargetBlank';
        }

        // NB: HTML.TargetNoreferrer and HTML.TargetNoopener must be AFTER HTML.TargetBlank
        // so that its post-attr-transform gets run afterwards.
        if ($config->get('HTML.TargetNoreferrer')) {
            $modules[] = 'TargetNoreferrer';
        }

        if ($config->get('HTML.TargetNoopener')) {
            $modules[] = 'TargetNoopener';
        }

        // merge in custom modules
        $modules = array_merge($modules, $this->userModules);

        foreach ($modules as $module) {
            $this->processModule($module);
            $this->modules[$module]->setup($config);
        }

        foreach ($this->doctype->tidyModules as $module) {
            $this->processModule($module);
            $this->modules[$module]->setup($config);
        }

        // prepare any injectors
        foreach ($this->modules as $module) {
            $n = [];
            foreach ($module->info_injector as $injector) {
                if (!is_object($injector)) {
                    $class = "HTMLPurifier\\Injector\\$injector";
                    $injector = new $class();
                }
                $n[$injector->name] = $injector;
            }
            $module->info_injector = $n;
        }

        // setup lookup table based on all valid modules
        foreach ($this->modules as $module) {
            foreach ($module->info as $name => $def) {
                if (!isset($this->elementLookup[$name])) {
                    $this->elementLookup[$name] = [];
                }
                $this->elementLookup[$name][] = $module->name;
            }
        }

        // note the different choice
        $this->contentSets = new ContentSets(
        // content set assembly deals with all possible modules,
        // not just ones deemed to be "safe"
            $this->modules
        );

        $this->attrCollections = new AttrCollections(
            $this->attrTypes,
            // there is no way to directly disable a global attribute,
            // but using AllowedAttributes or simply not including
            // the module in your custom doctype should be sufficient
            $this->modules
        );
    }

    /**
     * Takes a module and adds it to the active module collection,
     * registering it if necessary.
     *
     * @param string|HTMLModule $module
     *
     * @return void
     * @throws Exception
     */
    public function processModule($module): void
    {
        if (is_object($module) || !isset($this->registeredModules[$module])) {
            $this->registerModule($module);
        }

        if ($module instanceof HTMLModule) {
            $this->modules[$module->name] = $this->registeredModules[$module->name];
        } else {
            $this->modules[$module] = $this->registeredModules[$module];
        }
    }

    /**
     * Retrieves merged element definitions.
     *
     * @return array<string, ElementDef>
     */
    public function getElements(): array
    {
        $elements = [];
        foreach ($this->modules as $module) {
            if (!$this->trusted && !$module->safe) {
                continue;
            }
            foreach ($module->info as $name => $v) {
                if (isset($elements[$name])) {
                    continue;
                }
                $elements[$name] = $this->getElement($name);
            }
        }

        // remove dud elements, this happens when an element that
        // appeared to be safe actually wasn't
        foreach ($elements as $n => $v) {
            if ($v === false) {
                unset($elements[$n]);
            }
        }

        return array_filter($elements);
    }

    /**
     * Retrieves a single merged element definition
     *
     * @param string $name    Name of element
     * @param bool   $trusted Boolean trusted overriding parameter: set to true
     *                        if you want the full version of an element
     *
     * @return ElementDef|false Merged HTMLPurifier\HTMLPurifier_ElementDef
     * @note You may notice that modules are getting iterated over twice (once
     *       in getElements() and once here). This
     *       is because
     */
    public function getElement(string $name, bool $trusted = null)
    {
        if (!isset($this->elementLookup[$name])) {
            return false;
        }

        // setup global state variables
        $def = false;
        if ($trusted === null) {
            $trusted = $this->trusted;
        }

        // iterate through each module that has registered itself to this
        // element
        foreach ($this->elementLookup[$name] as $module_name) {
            $module = $this->modules[$module_name];

            // refuse to create/merge from a module that is deemed unsafe--
            // pretend the module doesn't exist--when trusted mode is not on.
            if (!$trusted && !$module->safe) {
                continue;
            }

            // clone is used because, ideally speaking, the original
            // definition should not be modified. Usually, this will
            // make no difference, but for consistency's sake
            $new_def = clone $module->info[$name];

            if (!$def && $new_def->standalone) {
                $def = $new_def;
            } elseif ($def) {
                // This will occur even if $new_def is standalone. In practice,
                // this will usually result in a full replacement.
                $def->mergeIn($new_def);
            } else {
                // :TODO:
                // non-standalone definitions that don't have a standalone
                // to merge into could be deferred to the end
                // HOWEVER, it is perfectly valid for a non-standalone
                // definition to lack a standalone definition, even
                // after all processing: this allows us to safely
                // specify extra attributes for elements that may not be
                // enabled all in one place.  In particular, this might
                // be the case for trusted elements.  WARNING: care must
                // be taken that the /extra/ definitions are all safe.
                continue;
            }

            // attribute value expansions
            $this->attrCollections->performInclusions($def->attr);
            $this->attrCollections->expandIdentifiers($def->attr, $this->attrTypes);

            // descendants_are_inline, for ChildDef_Chameleon
            if (
                is_string($def->content_model)
                && $name !== 'del'
                && $name !== 'ins'
                && strpos($def->content_model, 'Inline') !== false
            ) {
                // this is for you, ins/del
                $def->descendants_are_inline = true;
            }

            $this->contentSets->generateChildDef($def, $module);
        }

        // This can occur if there is a blank definition, but no base to
        // mix it in with
        if (!$def) {
            return false;
        }

        // add information on required attributes
        foreach ($def->attr as $attr_name => $attr_def) {
            if ($attr_def->required) {
                $def->required_attr[] = $attr_name;
            }
        }

        return $def;
    }
}
