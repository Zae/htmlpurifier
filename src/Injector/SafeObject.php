<?php

declare(strict_types=1);

namespace HTMLPurifier\Injector;

use HTMLPurifier\Context;
use HTMLPurifier\Injector;
use HTMLPurifier\Token;
use HTMLPurifier_Config;
use HTMLPurifier_Exception;
use HTMLPurifier_Token_Empty;

/**
 * Adds important param elements to inside of object in order to make
 * things safe.
 */
class SafeObject extends Injector
{
    /**
     * @type string
     */
    public $name = 'SafeObject';

    /**
     * @type array
     */
    public $needed = ['object', 'param'];

    /**
     * @type array
     */
    protected $objectStack = [];

    /**
     * @type array
     */
    protected $paramStack = [];

    /**
     * Keep this synchronized with AttrTransform/SafeParam.php.
     *
     * @type array
     */
    protected $addParam = [
        'allowScriptAccess' => 'never',
        'allowNetworking' => 'internal',
    ];

    /**
     * These are all lower-case keys.
     *
     * @type array
     */
    protected $allowedParam = [
        'wmode' => true,
        'movie' => true,
        'flashvars' => true,
        'src' => true,
        'allowfullscreen' => true, // if omitted, assume to be 'false'
    ];

    /**
     * @param HTMLPurifier_Config $config
     * @param Context             $context
     *
     * @return void
     * @throws HTMLPurifier_Exception
     */
    public function prepare(HTMLPurifier_Config $config, Context $context)
    {
        parent::prepare($config, $context);
    }

    /**
     * @param Token $token
     */
    public function handleElement(Token &$token)
    {
        if ($token->name == 'object') {
            $this->objectStack[] = $token;
            $this->paramStack[] = [];
            $new = [$token];

            foreach ($this->addParam as $name => $value) {
                $new[] = new HTMLPurifier_Token_Empty('param', ['name' => $name, 'value' => $value]);
            }

            $token = $new;
        } elseif ($token->name === 'param') {
            $nest = count($this->currentNesting) - 1;
            if ($nest >= 0 && $this->currentNesting[$nest]->name === 'object') {
                $i = count($this->objectStack) - 1;
                if (!isset($token->attr['name'])) {
                    $token = false;

                    return;
                }

                $n = $token->attr['name'];
                // We need this fix because YouTube doesn't supply a data
                // attribute, which we need if a type is specified. This is
                // *very* Flash specific.
                if (!isset($this->objectStack[$i]->attr['data']) &&
                    ($token->attr['name'] === 'movie' || $token->attr['name'] === 'src')
                ) {
                    $this->objectStack[$i]->attr['data'] = $token->attr['value'];
                }

                // Check if the parameter is the correct value but has not
                // already been added
                if (!isset($this->paramStack[$i][$n]) &&
                    isset($this->addParam[$n]) &&
                    $token->attr['name'] === $this->addParam[$n]) {
                    // keep token, and add to param stack
                    $this->paramStack[$i][$n] = true;
                } elseif (isset($this->allowedParam[strtolower($n)])) {
                    // keep token, don't do anything to it
                    // (could possibly check for duplicates here)
                    // Note: In principle, parameters should be case sensitive.
                    // But it seems they are not really; so accept any case.
                } else {
                    $token = false;
                }
            } else {
                // not directly inside an object, DENY!
                $token = false;
            }
        }
    }

    /**
     * Handler that is called when an end token is processed
     *
     * @param Token $token
     */
    public function handleEnd(Token &$token): void
    {
        // This is the WRONG way of handling the object and param stacks;
        // we should be inserting them directly on the relevant object tokens
        // so that the global stack handling handles it.
        if ($token->name === 'object') {
            array_pop($this->objectStack);
            array_pop($this->paramStack);
        }
    }
}
