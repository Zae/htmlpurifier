<?php

declare(strict_types=1);

namespace HTMLPurifier;

use HTMLPurifier\ChildDef\Custom;
use HTMLPurifier\ChildDef\Nothing;
use HTMLPurifier\ChildDef\Optional;
use HTMLPurifier\ChildDef\Required;

/**
 * @todo Unit test
 */
class ContentSets
{
    /**
     * List of content set strings (pipe separators) indexed by name.
     *
     * @var array
     */
    public $info = [];

    /**
     * List of content set lookups (element => true) indexed by name.
     *
     * @var array
     * @note This is in HTMLPurifier\HTMLPurifier_HTMLDefinition->info_content_sets
     */
    public $lookup = [];

    /**
     * Synchronized list of defined content sets (keys of info).
     *
     * @var array
     */
    protected $keys = [];

    /**
     * Synchronized list of defined content values (values of info).
     *
     * @var array
     */
    protected $values = [];

    /**
     * Merges in module's content sets, expands identifiers in the content
     * sets and populates the keys, values and lookup member variables.
     *
     * @param HTMLModule[] $modules List of HTMLPurifier\HTMLPurifier_HTMLModule
     */
    public function __construct(array $modules)
    {
        // populate content_sets based on module hints
        // sorry, no way of overloading
        foreach ($modules as $module) {
            foreach ($module->content_sets as $key => $value) {
                $temp = $this->convertToLookup($value);
                if (isset($this->lookup[$key])) {
                    // add it into the existing content set
                    $this->lookup[$key] = array_merge($this->lookup[$key], $temp);
                } else {
                    $this->lookup[$key] = $temp;
                }
            }
        }

        $old_lookup = false;
        while ($old_lookup !== $this->lookup) {
            $old_lookup = $this->lookup;
            foreach ($this->lookup as $i => $set) {
                $add = [];
                foreach ($set as $element => $x) {
                    if (isset($this->lookup[$element])) {
                        $add += $this->lookup[$element];
                        unset($this->lookup[$i][$element]);
                    }
                }
                $this->lookup[$i] += $add;
            }
        }

        foreach ($this->lookup as $key => $lookup) {
            $this->info[$key] = implode(' | ', array_keys($lookup));
        }

        $this->keys = array_keys($this->info);
        $this->values = array_values($this->info);
    }

    /**
     * Accepts a definition; generates and assigns a ChildDef for it
     *
     * @param ElementDef $def    HTMLPurifier\HTMLPurifier_ElementDef reference
     * @param HTMLModule $module Module that defined the ElementDef
     */
    public function generateChildDef(ElementDef $def, HTMLModule $module): void
    {
        if (!empty($def->child)) { // already done!
            return;
        }

        $content_model = $def->content_model;
        // Assume that $this->keys is alphanumeric
        $def->content_model = preg_replace_callback(
            '/\b(' . implode('|', $this->keys) . ')\b/',
            [$this, 'generateChildDefCallback'],
            $content_model
        );

        $def->child = $this->getChildDef($def, $module);
    }

    /**
     * @param array $matches
     * @return mixed
     */
    public function generateChildDefCallback(array $matches)
    {
        return $this->info[$matches[0]];
    }

    /**
     * Instantiates a ChildDef based on content_model and content_model_type
     * member variables in HTMLPurifier\HTMLPurifier_ElementDef
     *
     * @note This will also defer to modules for custom HTMLPurifier\HTMLPurifier_ChildDef
     *       subclasses that need content set expansion
     *
     * @param ElementDef $def    HTMLPurifier\HTMLPurifier_ElementDef to have ChildDef extracted
     * @param HTMLModule $module Module that defined the ElementDef
     *
     * @return ChildDef|null corresponding to ElementDef
     */
    public function getChildDef(ElementDef $def, HTMLModule $module): ?ChildDef
    {
        $value = $def->content_model;

        /**
         * @psalm-suppress DocblockTypeContradiction
         */
        if (\is_object($value)) {
            trigger_error(
                'Literal object child definitions should be stored in ' .
                'ElementDef->child not ElementDef->content_model',
                E_USER_NOTICE
            );

            return null;
        }

        switch ($def->content_model_type) {
            case 'required':
                return new Required($value);
            case 'optional':
                return new Optional($value);
            case 'empty':
                return new Nothing();
            case 'custom':
                return new Custom($value);
        }

        // defer to its module
        $return = false;
        if ($module->defines_child_def) { // save a func call
            $return = $module->getChildDef($def);
        }

        if ($return !== false) {
            return $return;
        }

        // error-out
        trigger_error(
            'Could not determine which ChildDef class to instantiate',
            E_USER_ERROR
        );

        return null;
    }

    /**
     * Converts a string list of elements separated by pipes into
     * a lookup array.
     *
     * @param string $string List of elements
     * @return array Lookup array of elements
     */
    protected function convertToLookup(string $string): array
    {
        $array = explode('|', str_replace(' ', '', $string));
        $ret = [];
        foreach ($array as $k) {
            $ret[$k] = true;
        }

        return $ret;
    }
}
