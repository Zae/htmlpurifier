<?php

declare(strict_types=1);

namespace HTMLPurifier\ChildDef;

use HTMLPurifier\Context;
use HTMLPurifier\Node\Element;
use HTMLPurifier\Node\Text;
use HTMLPurifier\Config;
use HTMLPurifier\Exception;

/**
 * Takes the contents of blockquote when in strict and reformats for validation.
 */
class StrictBlockquote extends Required
{
    /**
     * @var array
     */
    protected $real_elements = [];

    /**
     * @var array
     */
    protected $fake_elements = [];

    /**
     * @var bool
     */
    public $allow_empty = true;

    /**
     * @var string
     */
    public $type = 'strictblockquote';

    /**
     * @var bool
     */
    protected $init = false;

    /**
     * @param Config $config
     *
     * @return array
     * @note We don't want MakeWellFormed to auto-close inline elements since
     *       they might be allowed.
     */
    public function getAllowedElements(Config $config): array
    {
        $this->init($config);

        return $this->fake_elements;
    }

    /**
     * @param array   $children
     * @param Config  $config
     * @param Context $context
     *
     * @return array
     * @throws Exception
     */
    public function validateChildren(array $children, Config $config, Context $context): array
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
                if (
                    (
                        ($node instanceof Text && !$node->is_whitespace)
                        || ($node instanceof Element && !isset($this->elements[$node->name]))
                    )
                    && !\is_null($def)
                ) {
                    $block_wrap = new Element($def->info_block_wrapper);
                    $ret[] = $block_wrap;
                }
            } elseif ($node instanceof Element && isset($this->elements[$node->name])) {
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
     * @param Config $config
     *
     * @throws Exception
     */
    private function init(Config $config): void
    {
        if (!$this->init) {
            $def = $config->getHTMLDefinition();

            // allow all inline elements
            $this->real_elements = $this->elements;
            $this->fake_elements = $def->info_content_sets['Flow'] ?? [];
            $this->fake_elements['#PCDATA'] = true;
            $this->init = true;
        }
    }
}
