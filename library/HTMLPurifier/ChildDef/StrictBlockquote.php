<?php

declare(strict_types=1);

use HTMLPurifier\Node\Element;
use HTMLPurifier\Node\Text;

/**
 * Takes the contents of blockquote when in strict and reformats for validation.
 */
class HTMLPurifier_ChildDef_StrictBlockquote extends HTMLPurifier_ChildDef_Required
{
    /**
     * @type array
     */
    protected $real_elements;

    /**
     * @type array
     */
    protected $fake_elements;

    /**
     * @type bool
     */
    public $allow_empty = true;

    /**
     * @type string
     */
    public $type = 'strictblockquote';

    /**
     * @type bool
     */
    protected $init = false;

    /**
     * @param HTMLPurifier_Config $config
     *
     * @return array
     * @note We don't want MakeWellFormed to auto-close inline elements since
     *       they might be allowed.
     */
    public function getAllowedElements(HTMLPurifier_Config $config): array
    {
        $this->init($config);

        return $this->fake_elements;
    }

    /**
     * @param array                $children
     * @param HTMLPurifier_Config  $config
     * @param HTMLPurifier_Context $context
     *
     * @return array
     * @throws HTMLPurifier_Exception
     */
    public function validateChildren(array $children, HTMLPurifier_Config $config, HTMLPurifier_Context $context): array
    {
        $this->init($config);

        // trick the parent class into thinking it allows more
        $this->elements = $this->fake_elements;
        $result = parent::validateChildren($children, $config, $context);
        $this->elements = $this->real_elements;

        if ($result === false) {
            return [];
        }

        if ($result === true) {
            $result = $children;
        }

        $def = $config->getHTMLDefinition();
        $block_wrap = false;
        $ret = [];

        foreach ($result as $node) {
            if ($block_wrap === false) {
                if (($node instanceof Text && !$node->is_whitespace) ||
                    ($node instanceof Element && !isset($this->elements[$node->name]))) {
                    $block_wrap = new Element($def->info_block_wrapper);
                    $ret[] = $block_wrap;
                }
            } else if ($node instanceof Element && isset($this->elements[$node->name])) {
                $block_wrap = false;
            }

            if ($block_wrap) {
                $block_wrap->children[] = $node;
            } else {
                $ret[] = $node;
            }
        }

        return $ret;
    }

    /**
     * @param HTMLPurifier_Config $config
     *
     * @throws HTMLPurifier_Exception
     */
    private function init(HTMLPurifier_Config $config)
    {
        if (!$this->init) {
            $def = $config->getHTMLDefinition();

            // allow all inline elements
            $this->real_elements = $this->elements;
            $this->fake_elements = $def->info_content_sets['Flow'];
            $this->fake_elements['#PCDATA'] = true;
            $this->init = true;
        }
    }
}
