<?php

declare(strict_types=1);

use HTMLPurifier\HTMLDefinition;
use HTMLPurifier\Context;
use HTMLPurifier\ChildDef;
use HTMLPurifier\Exception;

/**
 * Class HTMLPurifier_Printer_HTMLDefinition
 */
class HTMLPurifier_Printer_HTMLDefinition extends HTMLPurifier_Printer
{
    /**
     * @type HTMLDefinition, for easy access
     */
    protected $def;

    /**
     * @param \HTMLPurifier\Config $config
     *
     * @return string
     * @throws Exception
     */
    public function render(\HTMLPurifier\Config $config): string
    {
        $ret = '';
        $this->config =& $config;

        $this->def = $config->getHTMLDefinition();

        $ret .= $this->start('div', ['class' => 'HTMLPurifier_Printer']);

        $ret .= $this->renderDoctype();
        $ret .= $this->renderEnvironment();
        $ret .= $this->renderContentSets();
        $ret .= $this->renderInfo();

        return $ret . $this->end('div');
    }

    /**
     * Renders the Doctype table
     *
     * @return string
     */
    protected function renderDoctype(): string
    {
        $doctype = $this->def->doctype;
        $ret = '';
        $ret .= $this->start('table');
        $ret .= $this->element('caption', 'Doctype');
        $ret .= $this->row('Name', $doctype->name);
        $ret .= $this->row('XML', $doctype->xml ? 'Yes' : 'No');
        $ret .= $this->row('Default Modules', implode(', ', $doctype->modules));
        $ret .= $this->row('Default Tidy Modules', implode(', ', $doctype->tidyModules));

        return $ret . $this->end('table');
    }

    /**
     * Renders environment table, which is miscellaneous info
     *
     * @return string
     */
    protected function renderEnvironment(): string
    {
        $def = $this->def;

        $ret = '';

        $ret .= $this->start('table');
        $ret .= $this->element('caption', 'Environment');

        $ret .= $this->row('Parent of fragment', $def->info_parent);
        $ret .= $this->renderChildren($def->info_parent_def->child);
        $ret .= $this->row('Block wrap name', $def->info_block_wrapper);

        $ret .= $this->start('tr');
        $ret .= $this->element('th', 'Global attributes');
        $ret .= $this->element('td', $this->listifyAttr($def->info_global_attr), null, 0);
        $ret .= $this->end('tr');

        $ret .= $this->start('tr');
        $ret .= $this->element('th', 'Tag transforms');
        $list = [];

        foreach ($def->info_tag_transform as $old => $new) {
            $new = $this->getClass($new, 'TagTransform_');
            $list[] = "<$old> with $new";
        }

        $ret .= $this->element('td', $this->listify($list));
        $ret .= $this->end('tr');

        $ret .= $this->start('tr');
        $ret .= $this->element('th', 'Pre-AttrTransform');
        $ret .= $this->element('td', $this->listifyObjectList($def->info_attr_transform_pre));
        $ret .= $this->end('tr');

        $ret .= $this->start('tr');
        $ret .= $this->element('th', 'Post-AttrTransform');
        $ret .= $this->element('td', $this->listifyObjectList($def->info_attr_transform_post));
        $ret .= $this->end('tr');

        return $ret . $this->end('table');
    }

    /**
     * Renders the Content Sets table
     *
     * @return string
     */
    protected function renderContentSets(): string
    {
        $ret = '';
        $ret .= $this->start('table');
        $ret .= $this->element('caption', 'Content Sets');

        foreach ($this->def->info_content_sets as $name => $lookup) {
            $ret .= $this->heavyHeader($name);
            $ret .= $this->start('tr');
            $ret .= $this->element('td', $this->listifyTagLookup($lookup));
            $ret .= $this->end('tr');
        }

        return $ret . $this->end('table');
    }

    /**
     * Renders the Elements ($info) table
     *
     * @return string
     */
    protected function renderInfo(): string
    {
        $ret = '';
        $ret .= $this->start('table');
        $ret .= $this->element('caption', 'Elements ($info)');

        ksort($this->def->info);

        $ret .= $this->heavyHeader('Allowed tags', 2);
        $ret .= $this->start('tr');
        $ret .= $this->element('td', $this->listifyTagLookup($this->def->info), ['colspan' => 2]);
        $ret .= $this->end('tr');

        foreach ($this->def->info as $name => $def) {
            $ret .= $this->start('tr');
            $ret .= $this->element('th', "<$name>", ['class' => 'heavy', 'colspan' => 2]);
            $ret .= $this->end('tr');
            $ret .= $this->start('tr');
            $ret .= $this->element('th', 'Inline content');
            $ret .= $this->element('td', $def->descendants_are_inline ? 'Yes' : 'No');
            $ret .= $this->end('tr');

            if (!empty($def->excludes)) {
                $ret .= $this->start('tr');
                $ret .= $this->element('th', 'Excludes');
                $ret .= $this->element('td', $this->listifyTagLookup($def->excludes));
                $ret .= $this->end('tr');
            }

            if (!empty($def->attr_transform_pre)) {
                $ret .= $this->start('tr');
                $ret .= $this->element('th', 'Pre-AttrTransform');
                $ret .= $this->element('td', $this->listifyObjectList($def->attr_transform_pre));
                $ret .= $this->end('tr');
            }

            if (!empty($def->attr_transform_post)) {
                $ret .= $this->start('tr');
                $ret .= $this->element('th', 'Post-AttrTransform');
                $ret .= $this->element('td', $this->listifyObjectList($def->attr_transform_post));
                $ret .= $this->end('tr');
            }

            if (!empty($def->auto_close)) {
                $ret .= $this->start('tr');
                $ret .= $this->element('th', 'Auto closed by');
                $ret .= $this->element('td', $this->listifyTagLookup($def->auto_close));
                $ret .= $this->end('tr');
            }

            $ret .= $this->start('tr');
            $ret .= $this->element('th', 'Allowed attributes');
            $ret .= $this->element('td', $this->listifyAttr($def->attr), [], false);
            $ret .= $this->end('tr');

            if (!empty($def->required_attr)) {
                $ret .= $this->row('Required attributes', $this->listify($def->required_attr));
            }

            $ret .= $this->renderChildren($def->child);
        }

        return $ret . $this->end('table');
    }

    /**
     * Renders a row describing the allowed children of an element
     *
     * @param ChildDef $def HTMLPurifier\HTMLPurifier_ChildDef of pertinent element
     *
     * @return string
     */
    protected function renderChildren(ChildDef $def): string
    {
        $context = new Context();

        $ret = '';
        $ret .= $this->start('tr');

        $elements = [];
        $attr = [];

        if (isset($def->elements)) {
            if ($def->type === 'strictblockquote') {
                $def->validateChildren([], $this->config, $context);
            }
            $elements = $def->elements;
        }

        if ($def->type == 'chameleon') {
            $attr['rowspan'] = 2;
        } elseif ($def->type == 'empty') {
            $elements = [];
        } elseif ($def->type == 'table') {
            $elements = array_flip(
                [
                    'col',
                    'caption',
                    'colgroup',
                    'thead',
                    'tfoot',
                    'tbody',
                    'tr'
                ]
            );
        }

        $ret .= $this->element('th', 'Allowed children', $attr);

        if ($def->type === 'chameleon') {
            $ret .= $this->element(
                'td',
                '<em>Block</em>: ' .
                $this->escape($this->listifyTagLookup($def->block->elements)),
                null,
                false
            );

            $ret .= $this->end('tr');
            $ret .= $this->start('tr');
            $ret .= $this->element(
                'td',
                '<em>Inline</em>: ' .
                $this->escape($this->listifyTagLookup($def->inline->elements)),
                null,
                false
            );

        } elseif ($def->type === 'custom') {

            $ret .= $this->element(
                'td',
                '<em>' . ucfirst($def->type) . '</em>: ' .
                $def->dtd_regex
            );

        } else {
            $ret .= $this->element(
                'td',
                '<em>' . ucfirst($def->type) . '</em>: ' .
                $this->escape($this->listifyTagLookup($elements)),
                null,
                false
            );
        }

        return $ret . $this->end('tr');
    }

    /**
     * Listifies a tag lookup table.
     *
     * @param array $array Tag lookup array in form of array('tagname' => true)
     *
     * @return string
     */
    protected function listifyTagLookup(array $array): string
    {
        ksort($array);

        $list = [];

        foreach ($array as $name => $discard) {
            if ($name !== '#PCDATA' && !isset($this->def->info[$name])) {
                continue;
            }
            $list[] = $name;
        }

        return $this->listify($list);
    }

    /**
     * Listifies a list of objects by retrieving class names and internal state
     *
     * @param array $array List of objects
     *
     * @return string
     * @todo Also add information about internal state
     */
    protected function listifyObjectList(array $array): string
    {
        ksort($array);

        $list = [];

        foreach ($array as $obj) {
            $list[] = $this->getClass($obj, 'AttrTransform_');
        }

        return $this->listify($list);
    }

    /**
     * Listifies a hash of attributes to AttrDef classes
     *
     * @param array $array Array hash in form of array('attrname' => HTMLPurifier_AttrDef)
     *
     * @return string
     */
    protected function listifyAttr(array $array): string
    {
        ksort($array);

        $list = [];

        foreach ($array as $name => $obj) {
            if ($obj === false) {
                continue;
            }

            $list[] = "$name&nbsp;=&nbsp;<i>" . $this->getClass($obj, 'AttrDef_') . '</i>';
        }

        return $this->listify($list);
    }

    /**
     * Creates a heavy header row
     *
     * @param string $text
     * @param int    $num
     *
     * @return string
     */
    protected function heavyHeader(string $text, int $num = 1): string
    {
        $ret = '';
        $ret .= $this->start('tr');
        $ret .= $this->element('th', $text, ['colspan' => $num, 'class' => 'heavy']);

        return $ret . $this->end('tr');
    }
}
