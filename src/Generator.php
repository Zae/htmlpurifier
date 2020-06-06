<?php

declare(strict_types=1);

namespace HTMLPurifier;

use HTMLPurifier\Token\End;
use HTMLPurifier\Token\Start;
use HTMLPurifier\Token\Comment;
use HTMLPurifier\Token\EmptyToken;
use HTMLPurifier\Token\Text;
use stdClass;
use Tidy;
use function count;
use function extension_loaded;

/**
 * Generates HTML from tokens.
 *
 * @todo Refactor interface so that configuration/context is determined
 *       upon instantiation, no need for messy generateFromTokens() calls
 * @todo Make some of the more internal functions protected, and have
 *       unit tests work around that
 */
class Generator
{
    /**
     * Whether or not generator should produce XML output.
     *
     * @type bool
     */
    private $xhtml = true;

    /**
     * :HACK: Whether or not generator should comment the insides of <script> tags.
     *
     * @type bool
     */
    private $scriptFix = false;

    /**
     * Cache of HTMLDefinition during HTML output to determine whether or
     * not attributes should be minimized.
     *
     * @type HTMLDefinition
     */
    private $def;

    /**
     * Cache of %Output.SortAttr.
     *
     * @type bool
     */
    private $sortAttr;

    /**
     * Cache of %Output.FlashCompat.
     *
     * @type bool
     */
    private $flashCompat;

    /**
     * Cache of %Output.FixInnerHTML.
     *
     * @type bool
     */
    private $innerHTMLFix;

    /**
     * Stack for keeping track of object information when outputting IE
     * compatibility code.
     *
     * @type array
     */
    private $flashStack = [];

    /**
     * Configuration for the generator
     *
     * @type Config
     */
    protected $config;

    /**
     * @param Config  $config
     * @param Context $context
     *
     * @throws Exception
     */
    public function __construct(Config $config, Context $context)
    {
        $this->config = $config;
        $this->scriptFix = $config->get('Output.CommentScriptContents');
        $this->innerHTMLFix = $config->get('Output.FixInnerHTML');
        $this->sortAttr = $config->get('Output.SortAttr');
        $this->flashCompat = $config->get('Output.FlashCompat');
        $this->def = $config->getHTMLDefinition();
        $this->xhtml = $this->def->doctype->xml ?? true;
    }

    /**
     * Generates HTML from an array of tokens.
     *
     * @param Token[] $tokens Array of HTMLPurifier\HTMLPurifier_Token
     *
     * @return string Generated HTML
     * @throws Exception
     */
    public function generateFromTokens(array $tokens): string
    {
        if (!$tokens) {
            return '';
        }

        // Basic algorithm
        $html = '';
        for ($i = 0, $size = count($tokens); $i < $size; $i++) {
            if (
                $this->scriptFix && $tokens[$i]->name === 'script'
                && $i + 2 < $size && $tokens[$i + 2] instanceof End
            ) {
                // script special case
                // the contents of the script block must be ONE token
                // for this to work.
                $html .= $this->generateFromToken($tokens[$i++]);
                $html .= $this->generateScriptFromToken($tokens[$i++]);
            }
            $html .= $this->generateFromToken($tokens[$i]);
        }

        // Tidy cleanup
        if (extension_loaded('tidy') && $this->config->get('Output.TidyFormat')) {
            $tidy = new Tidy();
            $tidy->parseString(
                $html,
                [
                    'indent' => true,
                    'output-xhtml' => $this->xhtml,
                    'show-body-only' => true,
                    'indent-spaces' => 2,
                    'wrap' => 68,
                ],
                'utf8'
            );
            $tidy->cleanRepair();

            /**
             * @psalm-suppress InvalidCast
             * @todo Install tidy as dev dependency so we can infer types in psalm?
             */
            $html = (string)$tidy; // explicit cast necessary
        }

        // Normalize newlines to system defined value
        if ($this->config->get('Core.NormalizeNewlines')) {
            $nl = $this->config->get('Output.Newline');
            if ($nl === null) {
                $nl = PHP_EOL;
            }
            if ($nl !== "\n") {
                $html = str_replace("\n", $nl, $html);
            }
        }

        return $html;
    }

    /**
     * Generates HTML from a single token.
     *
     * @param Token $token HTMLPurifier\HTMLPurifier_Token object.
     * @return string Generated HTML
     */
    public function generateFromToken(Token $token): string
    {
        if ($token instanceof Start) {
            $attr = $this->generateAttributes($token->attr, $token->name);
            if ($this->flashCompat && $token->name === 'object') {
                $flash = new stdClass();
                $flash->attr = $token->attr;
                $flash->param = [];
                $this->flashStack[] = $flash;
            }

            return '<' . $token->name . ($attr ? ' ' : '') . $attr . '>';
        }

        if ($token instanceof End) {
            $_extra = '';
            if ($this->flashCompat && $token->name === 'object' && !empty($this->flashStack)) {
                // doesn't do anything for now
            }

            return $_extra . '</' . $token->name . '>';
        }

        if ($token instanceof EmptyToken) {
            if ($this->flashCompat && $token->name === 'param' && !empty($this->flashStack)) {
                $this->flashStack[count($this->flashStack) - 1]->param[$token->attr['name']] = $token->attr['value'];
            }
            $attr = $this->generateAttributes($token->attr, $token->name);

            return '<' . $token->name . ($attr ? ' ' : '') . $attr .
                   ($this->xhtml ? ' /' : '') // <br /> v. <br>
                   . '>';
        }

        if ($token instanceof Text) {
            return $this->escape($token->data, ENT_NOQUOTES);
        }

        if ($token instanceof Comment) {
            return '<!--' . $token->data . '-->';
        }

        return '';
    }

    /**
     * Special case processor for the contents of script tags
     *
     * @param Token $token HTMLPurifier\HTMLPurifier_Token object.
     *
     * @return string
     * @warning This runs into problems if there's already a literal
     *          --> somewhere inside the script contents.
     */
    public function generateScriptFromToken(Token $token): string
    {
        if (!$token instanceof Text) {
            return $this->generateFromToken($token);
        }

        // Thanks <http://lachy.id.au/log/2005/05/script-comments>
        $data = preg_replace('#//\s*$#', '', $token->data);

        return '<!--//--><![CDATA[//><!--' . "\n" . trim($data) . "\n" . '//--><!]]>';
    }

    /**
     * Generates attribute declarations from attribute array.
     *
     * @note This does not include the leading or trailing space.
     *
     * @param array  $assoc_array_of_attributes Attribute array
     * @param string $element                   Name of element attributes are for, used to check
     *                                          attribute minimization.
     *
     * @return string Generated HTML fragment for insertion.
     */
    public function generateAttributes(array $assoc_array_of_attributes, string $element = ''): string
    {
        $html = '';
        if ($this->sortAttr) {
            ksort($assoc_array_of_attributes);
        }

        foreach ($assoc_array_of_attributes as $key => $value) {
            if (!$this->xhtml) {
                // Remove namespaced attributes
                if (strpos($key, ':') !== false) {
                    continue;
                }

                // Check if we should minimize the attribute: val="val" -> val
                if ($element && !empty($this->def->info[$element]->attr[$key]->minimized)) {
                    $html .= $key . ' ';
                    continue;
                }
            }

            // Workaround for Internet Explorer innerHTML bug.
            // Essentially, Internet Explorer, when calculating
            // innerHTML, omits quotes if there are no instances of
            // angled brackets, quotes or spaces.  However, when parsing
            // HTML (for example, when you assign to innerHTML), it
            // treats backticks as quotes.  Thus,
            //      <img alt="``" />
            // becomes
            //      <img alt=`` />
            // becomes
            //      <img alt='' />
            // Fortunately, all we need to do is trigger an appropriate
            // quoting style, which we do by adding an extra space.
            // This also is consistent with the W3C spec, which states
            // that user agents may ignore leading or trailing
            // whitespace (in fact, most don't, at least for attributes
            // like alt, but an extra space at the end is barely
            // noticeable).  Still, we have a configuration knob for
            // this, since this transformation is not necesary if you
            // don't process user input with innerHTML or you don't plan
            // on supporting Internet Explorer.
            if ($this->innerHTMLFix) {
                if (strpos($value, '`') !== false) {
                    // check if correct quoting style would not already be
                    // triggered
                    if (strcspn($value, '"\' <>') === \strlen($value)) {
                        // protect!
                        $value .= ' ';
                    }
                }
            }

            $html .= $key . '="' . $this->escape($value) . '" ';
        }

        return rtrim($html);
    }

    /**
     * Escapes raw text data.
     *
     * @param string $string String data to escape for HTML.
     * @param int    $quote  Quoting style, like htmlspecialchars. ENT_NOQUOTES is
     *                       permissible for non-attribute output.
     *
     * @return string escaped data.
     * @todo This really ought to be protected, but until we have a facility
     *       for properly generating HTML here w/o using tokens, it stays
     *       public.
     */
    public function escape(string $string, int $quote = null): string
    {
        // Workaround for APC bug on Mac Leopard reported by sidepodcast
        // http://htmlpurifier.org/phorum/read.php?3,4823,4846
        if ($quote === null) {
            $quote = ENT_COMPAT;
        }

        return htmlspecialchars($string, $quote, 'UTF-8');
    }
}
