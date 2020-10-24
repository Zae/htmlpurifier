<?php

declare(strict_types=1);

namespace HTMLPurifier\ChildDef;

use HTMLPurifier\Context;
use HTMLPurifier\ChildDef;
use HTMLPurifier\Node;
use HTMLPurifier\Config;

/**
 * Custom validation class, accepts DTD child definitions
 *
 * @warning Currently this class is an all or nothing proposition, that is,
 *          it will only give a bool return value.
 */
class Custom extends ChildDef
{
    /**
     * @var string
     */
    public $type = 'custom';

    /**
     * @var bool
     */
    public $allow_empty = false;

    /**
     * Allowed child pattern as defined by the DTD.
     *
     * @var string
     */
    public $dtd_regex;

    /**
     * PCRE regex derived from $dtd_regex.
     *
     * @var string
     */
    public $pcre_regex;

    /**
     * @param string $dtd_regex Allowed child pattern from the DTD
     */
    public function __construct(string $dtd_regex)
    {
        $this->dtd_regex = $dtd_regex;

        $this->compileRegex();
    }

    /**
     * Compiles the PCRE regex from a DTD regex ($dtd_regex to $_pcre_regex)
     */
    protected function compileRegex(): void
    {
        $raw = str_replace(' ', '', $this->dtd_regex);
        if ($raw[0] !== '(') {
            $raw = "($raw)";
        }
        $el = '[#a-zA-Z0-9_.-]+';
        $reg = $raw;

        // COMPLICATED! AND MIGHT BE BUGGY! I HAVE NO CLUE WHAT I'M
        // DOING! Seriously: if there's problems, please report them.

        // collect all elements into the $elements array
        preg_match_all("/$el/", $reg, $matches);
        foreach ($matches[0] as $match) {
            $this->elements[$match] = true;
        }

        // setup all elements as parenthetical with leading commas
        $reg = preg_replace("/$el/", '(,\\0)', $reg);

        // remove commas when they were not solicited
        $reg = preg_replace("/([^,(|]\(+),/", '\\1', $reg);

        // remove all non-parenthetical commas: they are handled by first regex
        $reg = preg_replace("/,\(/", '(', $reg);

        $this->pcre_regex = $reg;
    }

    /**
     * @param Node[]  $children
     * @param Config  $config
     * @param Context $context
     *
     * @return bool
     */
    public function validateChildren(array $children, Config $config, Context $context): bool
    {
        $list_of_children = '';

        foreach ($children as $node) {
            if (!empty($node->is_whitespace)) {
                continue;
            }

            $list_of_children .= $node->name . ',';
        }

        // add leading comma to deal with stray comma declarations
        $list_of_children = ',' . rtrim($list_of_children, ',');
        $okay = preg_match(
            '/^,?' . $this->pcre_regex . '$/',
            $list_of_children
        );

        return (bool)$okay;
    }
}
