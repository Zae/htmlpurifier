<?php

declare(strict_types=1);

namespace HTMLPurifier;

/**
 * Represents an XHTML 1.1 module, with information on elements, tags
 * and attributes.
 *
 * @note Even though this is technically XHTML 1.1, it is also used for
 *       regular HTML parsing. We are using modulization as a convenient
 *       way to represent the internals of HTMLDefinition, and our
 *       implementation is by no means conforming and does not directly
 *       use the normative DTDs or XML schemas.
 * @note The public variables in a module should almost directly
 *       correspond to the variables in HTMLPurifier\HTMLPurifier_HTMLDefinition.
 *       However, the prefix info carries no special meaning in these
 *       objects (include it anyway if that's the correspondence though).
 * @todo Consider making some member functions protected
 */
class HTMLModule
{
    // -- Overloadable ----------------------------------------------------

    /**
     * Short unique string identifier of the module.
     *
     * @var string
     */
    public $name = '';

    /**
     * Informally, a list of elements this module changes.
     * Not used in any significant way.
     *
     * @var array
     */
    public $elements = [];

    /**
     * Associative array of element names to element definitions.
     * Some definitions may be incomplete, to be merged in later
     * with the full definition.
     *
     * @var array
     */
    public $info = [];

    /**
     * Associative array of content set names to content set additions.
     * This is commonly used to, say, add an A element to the Inline
     * content set. This corresponds to an internal variable $content_sets
     * and NOT info_content_sets member variable of HTMLDefinition.
     *
     * @var array
     */
    public $content_sets = [];

    /**
     * Associative array of attribute collection names to attribute
     * collection additions. More rarely used for adding attributes to
     * the global collections. Example is the StyleAttribute module adding
     * the style attribute to the Core. Corresponds to HTMLDefinition's
     * attr_collections->info, since the object's data is only info,
     * with extra behavior associated with it.
     *
     * @var array
     */
    public $attr_collections = [];

    /**
     * Associative array of deprecated tag name to HTMLPurifier\HTMLPurifier_TagTransform.
     *
     * @var array
     */
    public $info_tag_transform = [];

    /**
     * List of HTMLPurifier\HTMLPurifier_AttrTransform to be performed before validation.
     *
     * @var array
     */
    public $info_attr_transform_pre = [];

    /**
     * List of HTMLPurifier\HTMLPurifier_AttrTransform to be performed after validation.
     *
     * @var array
     */
    public $info_attr_transform_post = [];

    /**
     * List of HTMLPurifier\HTMLPurifier_Injector to be performed during well-formedness fixing.
     * An injector will only be invoked if all of it's pre-requisites are met;
     * if an injector fails setup, there will be no error; it will simply be
     * silently disabled.
     *
     * @var array
     */
    public $info_injector = [];

    /**
     * Boolean flag that indicates whether or not getChildDef is implemented.
     * For optimization reasons: may save a call to a function. Be sure
     * to set it if you do implement getChildDef(), otherwise it will have
     * no effect!
     *
     * @var bool
     */
    public $defines_child_def = false;

    /**
     * Boolean flag whether or not this module is safe. If it is not safe, all
     * of its members are unsafe. Modules are safe by default (this might be
     * slightly dangerous, but it doesn't make much sense to force HTML Purifier,
     * which is based off of safe HTML, to explicitly say, "This is safe," even
     * though there are modules which are "unsafe")
     *
     * @var bool
     * @note Previously, safety could be applied at an element level granularity.
     *       We've removed this ability, so in order to add "unsafe" elements
     *       or attributes, a dedicated module with this property set to false
     *       must be used.
     */
    public $safe = true;

    /**
     * Retrieves a proper HTMLPurifier\HTMLPurifier_ChildDef subclass based on
     * content_model and content_model_type member variables of
     * the HTMLPurifier\HTMLPurifier_ElementDef class. There is a similar function
     * in HTMLPurifier\HTMLPurifier_HTMLDefinition.
     *
     * @param ElementDef $def
     *
     * @return ChildDef|null
     */
    public function getChildDef(ElementDef $def)
    {
        return null;
    }

    // -- Convenience -----------------------------------------------------

    /**
     * Convenience function that sets up a new element
     *
     * @param string          $element                    Name of element to add
     * @param string|null     $type                       What content set should element be registered to?
     *                                                    Set as false to skip this step.
     * @param string|ChildDef $contents                   Allowed children in form of:
     *                                                    "$content_model_type: $content_model"
     * @param array|string|null $attr_includes              What attribute collections to register to
     *                                                    element?
     * @param array           $attr                       What unique attributes does the element define?
     *
     * @return ElementDef Created element definition object, so you
     *         can set advanced parameters
     * @see ElementDef:: for in-depth descriptions of these parameters.
     */
    public function addElement(string $element, ?string $type, $contents, $attr_includes = [], array $attr = []): ElementDef
    {
        $this->elements[] = $element;

        // parse content_model
        [$content_model_type, $content_model] = $this->parseContents($contents);

        // merge in attribute inclusions
        if (!\is_null($attr_includes)) {
            $this->mergeInAttrIncludes($attr, $attr_includes);
        }

        // add element to content sets
        if ($type) {
            $this->addElementToContentSet($element, $type);
        }

        // create element
        $this->info[$element] = ElementDef::create(
            $content_model,
            $content_model_type,
            $attr
        );

        // literal object $contents means direct child manipulation
        if (!\is_string($contents)) {
            $this->info[$element]->child = $contents;
        }

        return $this->info[$element];
    }

    /**
     * Convenience function that creates a totally blank, non-standalone
     * element.
     *
     * @param string $element Name of element to create
     *
     * @return ElementDef Created element
     */
    public function addBlankElement(string $element): ElementDef
    {
        if (!isset($this->info[$element])) {
            $this->elements[] = $element;
            $this->info[$element] = new ElementDef();
            $this->info[$element]->standalone = false;
        } else {
            trigger_error("Definition for $element already exists in module, cannot redefine");
        }

        return $this->info[$element];
    }

    /**
     * Convenience function that registers an element to a content set
     *
     * @param string $element Element to register
     * @param string $type    Name content set (warning: case sensitive, usually upper-case
     *                        first letter)
     */
    public function addElementToContentSet(string $element, string $type): void
    {
        if (!isset($this->content_sets[$type])) {
            $this->content_sets[$type] = '';
        } else {
            $this->content_sets[$type] .= ' | ';
        }

        $this->content_sets[$type] .= $element;
    }

    /**
     * Convenience function that transforms single-string contents
     * into separate content model and content model type
     *
     * @param string|mixed $contents Allowed children in form of:
     *                         "$content_model_type: $content_model"
     *
     * @return array
     * @note If contents is an object, an array of two nulls will be
     *       returned, and the callee needs to take the original $contents
     *       and use it directly.
     */
    public function parseContents($contents): array
    {
        if (!\is_string($contents)) {
            return [null, null];
        } // defer

        switch ($contents) {
            // check for shorthand content model forms
        case 'Empty':
            return ['empty', ''];
        case 'Inline':
            return ['optional', 'Inline | #PCDATA'];
        case 'Flow':
            return ['optional', 'Flow | #PCDATA'];
        }

        [$content_model_type, $content_model] = explode(':', $contents);
        $content_model_type = strtolower(trim($content_model_type));
        $content_model = trim($content_model);

        return [$content_model_type, $content_model];
    }

    /**
     * Convenience function that merges a list of attribute includes into
     * an attribute array.
     *
     * @param array $attr          Reference to attr array to modify
     * @param array|string $attr_includes Array of includes / string include to merge in
     */
    public function mergeInAttrIncludes(&$attr, $attr_includes): void
    {
        if (!\is_array($attr_includes)) {
            if (empty($attr_includes)) {
                $attr_includes = [];
            } else {
                $attr_includes = [$attr_includes];
            }
        }

        $attr[0] = $attr_includes;
    }

    /**
     * Convenience function that generates a lookup table with boolean
     * true as value.
     *
     * @param string|array $list List of values to turn into a lookup
     *
     * @note You can also pass an arbitrary number of arguments in
     *       place of the regular argument
     * @return array array equivalent of list
     */
    public function makeLookup($list): array
    {
        $args = func_get_args();
        if (\is_string($list)) {
            $list = $args;
        }

        $ret = [];
        foreach ($list as $value) {
            if ($value === null) {
                continue;
            }

            $ret[$value] = true;
        }

        return $ret;
    }

    /**
     * Lazy load construction of the module after determining whether
     * or not it's needed, and also when a finalized configuration object
     * is available.
     *
     * @param Config $config
     */
    public function setup(Config $config): void
    {
    }
}
