<?php

declare(strict_types=1);

namespace HTMLPurifier;

use HTMLPurifier\Node\Element;
use HTMLPurifier\Token\End;
use HTMLPurifier\Token\Start;

/**
 * Converts a stream of HTMLPurifier\HTMLPurifier_Token into an HTMLPurifier\HTMLPurifier_Node,
 * and back again.
 *
 * @note This transformation is not an equivalence.  We mutate the input
 * token stream to make it so; see all [MUT] markers in code.
 */
class Arborize
{
    /**
     * @param array  $tokens
     * @param Config $config
     *
     * @return mixed
     * @throws Exception
     */
    public static function arborize(array $tokens, Config $config)
    {
        $definition = $config->getHTMLDefinition();
        if (\is_null($definition)) {
            throw new Exception('No Definition found');
        }
        $parent = new Start($definition->info_parent);
        $stack = [$parent->toNode()];

        foreach ($tokens as $token) {
            $token->skip = null; // [MUT]
            $token->carryover = null; // [MUT]
            if ($token instanceof End) {
                $token->start = null; // [MUT]
                $r = array_pop($stack);
                //assert($r->name === $token->name);
                //assert(empty($token->attr));
                $r->endCol = $token->col;
                $r->endLine = $token->line;
                $r->endArmor = $token->armor;
                continue;
            }

            $node = $token->toNode();

            /** @var Element $element */
            $element = $stack[\count($stack) - 1];
            $element->children[] = $node;

            if ($token instanceof Start) {
                $stack[] = $node;
            }
        }

        //assert(count($stack) == 1);
        return $stack[0];
    }

    /**
     * @param Node $node
     * @return array
     */
    public static function flatten(Node $node): array
    {
        $level = 0;
        $nodes = [$level => new Queue([$node])];
        $closingTokens = [];
        $tokens = [];
        do {
            while (!$nodes[$level]->isEmpty()) {
                $node = $nodes[$level]->shift(); // FIFO
                [$start, $end] = $node->toTokenPair();
                if ($level > 0) {
                    $tokens[] = $start;
                }
                if ($end !== null) {
                    $closingTokens[$level][] = $end;
                }
                if ($node instanceof Element) {
                    $level++;
                    $nodes[$level] = new Queue();
                    foreach ($node->children as $childNode) {
                        $nodes[$level]->push($childNode);
                    }
                }
            }
            $level--;
            if ($level && isset($closingTokens[$level])) {
                while ($token = array_pop($closingTokens[$level])) {
                    $tokens[] = $token;
                }
            }
        } while ($level > 0);

        return $tokens;
    }
}
