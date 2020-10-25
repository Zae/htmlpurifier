<?php

declare(strict_types=1);

namespace HTMLPurifier;

use HTMLPurifier\Token\End;
use HTMLPurifier\Token\Start;
use HTMLPurifier\Token\Text;

use function count;

/**
 * Injects tokens into the document while parsing for well-formedness.
 * This enables "formatter-like" functionality such as auto-paragraphing,
 * smiley-ification and linkification to take place.
 *
 * A note on how handlers create changes; this is done by assigning a new
 * value to the $token reference. These values can take a variety of forms and
 * are best described HTMLPurifier\Strategy\HTMLPurifier_Strategy_MakeWellFormed->processToken()
 * documentation.
 *
 * @todo Allow injectors to request a re-run on their output. This
 *       would help if an operation is recursive.
 */
abstract class Injector
{
    /**
     * Advisory name of injector, this is for friendly error messages.
     *
     * @var string
     */
    public $name = '';

    /**
     * @var HTMLDefinition|null
     */
    protected $htmlDefinition;

    /**
     * Reference to CurrentNesting variable in Context. This is an array
     * list of tokens that we are currently "inside"
     *
     * @var array
     */
    protected $currentNesting = [];

    /**
     * Reference to current token.
     *
     * @var Token|null
     */
    protected $currentToken;

    /**
     * Reference to InputZipper variable in Context.
     *
     * @var Zipper|null
     */
    protected $inputZipper;

    /**
     * Array of elements and attributes this injector creates and therefore
     * need to be allowed by the definition. Takes form of
     * array('element' => array('attr', 'attr2'), 'element2')
     *
     * @var array
     */
    public $needed = [];

    /**
     * Number of elements to rewind backwards (relative).
     *
     * @var bool|int
     */
    protected $rewindOffset = false;

    /**
     * Rewind to a spot to re-perform processing. This is useful if you
     * deleted a node, and now need to see if this change affected any
     * earlier nodes. Rewinding does not affect other injectors, and can
     * result in infinite loops if not used carefully.
     *
     * @param bool|int $offset
     *
     * @warning HTML Purifier will prevent you from fast-forwarding with this
     *          function.
     */
    public function rewindOffset($offset): void
    {
        $this->rewindOffset = $offset;
    }

    /**
     * Retrieves rewind offset, and then unsets it.
     *
     * @return bool|int
     */
    public function getRewindOffset()
    {
        $r = $this->rewindOffset;
        $this->rewindOffset = false;

        return $r;
    }

    /**
     * Prepares the injector by giving it the config and context objects:
     * this allows references to important variables to be made within
     * the injector. This function also checks if the HTML environment
     * will work with the Injector (see checkNeeded()).
     *
     * @param Config  $config
     * @param Context $context
     *
     * @return bool|string Boolean false if success, string of missing needed element/attribute if failure
     * @throws Exception
     */
    public function prepare(Config $config, Context $context)
    {
        $this->htmlDefinition = $config->getHTMLDefinition();

        // Even though this might fail, some unit tests ignore this and
        // still test checkNeeded, so be careful. Maybe get rid of that
        // dependency.
        $result = $this->checkNeeded($config);
        if ($result !== false) {
            return $result;
        }

        $this->currentNesting =& $context->get('CurrentNesting');
        $this->currentToken =& $context->get('CurrentToken');
        $this->inputZipper =& $context->get('InputZipper');

        return false;
    }

    /**
     * This function checks if the HTML environment
     * will work with the Injector: if p tags are not allowed, the
     * Auto-Paragraphing injector should not be enabled.
     *
     * @param Config $config
     *
     * @return bool|string Boolean false if success, string of missing needed element/attribute if failure
     * @throws Exception
     */
    public function checkNeeded(Config $config)
    {
        $def = $config->getHTMLDefinition();
        foreach ($this->needed as $element => $attributes) {
            if (\is_int($element)) {
                $element = $attributes;
            }

            if (!isset($def->info[$element])) {
                return $element;
            }

            if (!\is_array($attributes)) {
                continue;
            }

            foreach ($attributes as $name) {
                if (!isset($def->info[$element]->attr[$name])) {
                    return "$element.$name";
                }
            }
        }

        return false;
    }

    /**
     * Tests if the context node allows a certain element
     *
     * @param string $name Name of element to test for
     * @return bool True if element is allowed, false if it is not
     */
    public function allowsElement(string $name): bool
    {
        if (!empty($this->currentNesting)) {
            $parent_token = array_pop($this->currentNesting);
            $this->currentNesting[] = $parent_token;
            /**
             * @psalm-suppress PossiblyNullArrayAccess
             * @psalm-suppress PossiblyNullPropertyFetch
             */
            $parent = $this->htmlDefinition->info[$parent_token->name];
        } else {
            /**
             * @psalm-suppress PossiblyNullPropertyFetch
             */
            $parent = $this->htmlDefinition->info_parent_def;
        }

        if (!isset($parent->child->elements[$name]) || isset($parent->excludes[$name])) {
            return false;
        }

        // check for exclusion
        if (!empty($this->currentNesting)) {
            for ($i = count($this->currentNesting) - 2; $i >= 0; $i--) {
                $node = $this->currentNesting[$i];
                /**
                 * @psalm-suppress PossiblyNullArrayAccess
                 * @psalm-suppress PossiblyNullPropertyFetch
                 */
                $def = $this->htmlDefinition->info[$node->name];

                if (isset($def->excludes[$name])) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Iterator function, which starts with the next token and continues until
     * you reach the end of the input tokens.
     *
     * @warning Please prevent previous references from interfering with this
     *          functions by setting $i = null beforehand!
     *
     * @param int   $i       Current integer index variable for inputTokens
     * @param Token $current Current token variable.
     *                       Do NOT use $token, as that variable is also a reference
     *
     * @return bool
     */
    protected function forward(?int &$i, ?Token &$current): bool
    {
        if ($this->inputZipper === null) {
            return false;
        }

        if ($i === null) {
            $i = count($this->inputZipper->back) - 1;
        } else {
            $i--;
        }

        if ($i < 0) {
            return false;
        }

        $current = $this->inputZipper->back[$i];

        return true;
    }

    /**
     * Similar to _forward, but accepts a third parameter $nesting (which
     * should be initialized at 0) and stops when we hit the end tag
     * for the node $this->inputIndex starts in.
     *
     * @param int   $i       Current integer index variable for inputTokens
     * @param Token $current Current token variable.
     *                       Do NOT use $token, as that variable is also a reference
     * @param int   $nesting
     *
     * @return bool
     */
    protected function forwardUntilEndToken(?int &$i, ?Token &$current, ?int &$nesting): bool
    {
        $result = $this->forward($i, $current);

        if (!$result) {
            return false;
        }

        if ($nesting === null) {
            $nesting = 0;
        }

        if ($current instanceof Start) {
            $nesting++;
        } elseif ($current instanceof End) {
            if ($nesting <= 0) {
                return false;
            }

            $nesting--;
        }

        return true;
    }

    /**
     * Iterator function, starts with the previous token and continues until
     * you reach the beginning of input tokens.
     *
     * @warning Please prevent previous references from interfering with this
     *          functions by setting $i = null beforehand!
     *
     * @param int   $i       Current integer index variable for inputTokens
     * @param Token $current Current token variable.
     *                       Do NOT use $token, as that variable is also a reference
     *
     * @return bool
     */
    protected function backward(?int &$i, ?Token &$current): bool
    {
        if ($this->inputZipper === null) {
            return false;
        }

        if ($i === null) {
            $i = count($this->inputZipper->front) - 1;
        } else {
            $i--;
        }

        if ($i < 0) {
            return false;
        }

        $current = $this->inputZipper->front[$i];

        return true;
    }

    /**
     * Handler that is called when a text token is processed
     *
     * @param Text $token
     *
     * @return void
     */
    public function handleText(Text &$token): void
    {
    }

    /**
     * Handler that is called when a start or empty token is processed
     *
     * @param Token $token
     *
     * @return void
     */
    public function handleElement(Token &$token): void
    {
    }

    /**
     * Handler that is called when an end token is processed
     *
     * @param End $token
     *
     * @return void
     */
    public function handleEnd(End &$token): void
    {
    }

    /**
     * Notifier that is called when an end token is processed
     *
     * @param Token $token Current token variable.
     *
     * @note This differs from handlers in that the token is read-only
     * @deprecated
     */
    public function notifyEnd($token): void
    {
    }

    /**
     * @param string $injector
     * @return self
     *
     * @psalm-param class-string $injector
     * @psalm-suppress LessSpecificReturnStatement
     * @psalm-suppress MoreSpecificReturnType
     */
    public static function make(string $injector): self
    {
        $class = sprintf(
            "%s\\%s",
            __CLASS__,
            $injector
        );

        return new $class();
    }
}
