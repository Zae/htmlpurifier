<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\Arborize;
use HTMLPurifier\Generator;
use HTMLPurifier\Lexer\DirectLex;
use HTMLPurifier\Node;
use HTMLPurifier\Node\Element;
use function is_array;
use function is_bool;
use function is_string;

/**
 * Class ComplexTestCase
 *
 * @package HTMLPurifier\Tests\Unit
 */
class ComplexTestCase extends TestCase
{
    /**
     * Instance of the object that will execute the method.
     * @type object
     */
    protected $obj;

    /**
     * Name of the function to be executed.
     * @type string
     */
    protected $func;

    /**
     * Whether or not the method deals in tokens.
     * If set to true, assertResult()
     * will transparently convert HTML to and back from tokens.
     * @type bool
     */
    protected $to_tokens = false;

    /**
     * Whether or not the method deals in a node list.
     * If set to true, assertResult() will transparently convert HTML
     * to and back from node.
     * @type bool
     */
    protected $to_node_list = false;

    /**
     * Whether or not to convert tokens back into HTML before performing
     * equality check, has no effect on bools.
     * @type bool
     */
    protected $to_html = false;

    /**
     * Instance of an HTMLPurifier_Lexer implementation.
     * @type HTMLPurifier_Lexer
     */
    protected $lexer;

    public function __construct()
    {
        $this->lexer = new DirectLex();
        parent::__construct();
    }

    /**
     * Asserts a specific result from a one parameter + config/context function
     *
     * @param string      $input  Input parameter
     * @param bool|string $expect Expectation
     *
     * @throws \HTMLPurifier\Exception
     */
    protected function assertResult(string $input, $expect = true): void
    {
        // $func may cause $input to change, so "clone" another copy
        // to sacrifice
        if ($this->to_node_list && is_string($input)) {
            $input = Arborize::arborize($this->tokenize($temp = $input), $this->config)->children;
            $input_c = Arborize::arborize($this->tokenize($temp), $this->config)->children;
        } elseif ($this->to_tokens && is_string($input)) {
            $input   = $this->tokenize($temp = $input);
            $input_c = $this->tokenize($temp);
        } else {
            $input_c = $input;
        }

        // call the function
        $func = $this->func;
        $result = $this->obj->$func($input_c, $this->config, $this->context);

        // test a bool result
        if (is_bool($result)) {
            static::assertEquals($expect, $result);
            return;
        }

        if (is_bool($expect)) {
            $expect = $input;
        }

        if ($this->to_html) {
            if ($this->to_node_list) {
                $result = $this->generateTokens($result);
                if (is_array($expect) && !empty($expect) && $expect[0] instanceof Node) {
                    $expect = $this->generateTokens($expect);
                }
            }
            $result = $this->generate($result);
            if (is_array($expect)) {
                $expect = $this->generate($expect);
            }
        }
        static::assertEquals($expect, $result);

        if ($expect !== $result) {
            echo '<pre>' . var_dump($result) . '</pre>';
        }
    }

    /**
     * Tokenize HTML into tokens, uses member variables for common variables
     *
     * @param $html
     *
     * @return array|\HTMLPurifier\Token[]
     * @throws \HTMLPurifier\Exception
     */
    protected function tokenize($html): array
    {
        return $this->lexer->tokenizeHTML($html, $this->config, $this->context);
    }

    /**
     * Generate textual HTML from tokens
     *
     * @param $tokens
     *
     * @return string
     * @throws \HTMLPurifier\Exception
     */
    protected function generate($tokens): string
    {
        $generator = new Generator($this->config, $this->context);
        return $generator->generateFromTokens($tokens);
    }

    /**
     * Generate tokens from node list
     *
     * @param $children
     *
     * @return array
     */
    protected function generateTokens($children): array
    {
        $dummy = new Element('dummy');
        $dummy->children = $children;

        return Arborize::flatten($dummy);
    }
}
