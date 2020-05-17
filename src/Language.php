<?php

declare(strict_types=1);

namespace HTMLPurifier;

use HTMLPurifier\Token\Tag;

/**
 * Represents a language and defines localizable string formatting and
 * other functions, as well as the localized messages for HTML Purifier.
 */
class Language
{
    /**
     * ISO 639 language code of language. Prefers shortest possible version.
     *
     * @type string
     */
    public $code = 'en';

    /**
     * Fallback language code.
     *
     * @type bool|string
     */
    public $fallback = false;

    /**
     * Array of localizable messages.
     *
     * @type array
     */
    public $messages = [];

    /**
     * Array of localizable error codes.
     *
     * @type array
     */
    public $errorNames = [];

    /**
     * True if no message file was found for this language, so English
     * is being used instead. Check this if you'd like to notify the
     * user that they've used a non-supported language.
     *
     * @type bool
     */
    public $error = false;

    /**
     * Has the language object been loaded yet?
     *
     * @type bool
     * @todo Make it private, fix usage in HTMLPurifier_LanguageTest
     */
    public $loaded = false;

    /**
     * @type Config
     */
    protected $config;

    /**
     * @type Context
     */
    protected $context;

    /**
     * @param Config  $config
     * @param Context $context
     */
    public function __construct($config, $context)
    {
        $this->config = $config;
        $this->context = $context;
    }

    /**
     * Loads language object with necessary info from factory cache
     *
     * @note This is a lazy loader
     */
    public function load(): void
    {
        if ($this->loaded) {
            return;
        }

        $factory = LanguageFactory::instance();
        $factory->loadLanguage($this->code);

        foreach ($factory->keys as $key) {
            $this->$key = $factory->cache[$this->code][$key];
        }

        $this->loaded = true;
    }

    /**
     * Retrieves a localised message.
     *
     * @param string $key string identifier of message
     *
     * @return string localised message
     */
    public function getMessage(string $key): string
    {
        if (!$this->loaded) {
            $this->load();
        }

        if (!isset($this->messages[$key])) {
            return "[$key]";
        }

        return $this->messages[$key];
    }

    /**
     * Retrieves a localised error name.
     *
     * @param int $int error number, corresponding to PHP's error reporting
     *
     * @return string localised message
     */
    public function getErrorName(int $int): string
    {
        if (!$this->loaded) {
            $this->load();
        }

        if (!isset($this->errorNames[$int])) {
            return "[Error: $int]";
        }

        return $this->errorNames[$int];
    }

    /**
     * Converts an array list into a string readable representation
     *
     * @param array $array
     *
     * @return string
     */
    public function listify(array $array): string
    {
        $sep = $this->getMessage('Item separator');
        $sep_last = $this->getMessage('Item separator last');
        $ret = '';

        for ($i = 0, $c = \count($array); $i < $c; $i++) {
            if ($i === 0) {
                //do nothing
            } elseif ($i + 1 < $c) {
                $ret .= $sep;
            } else {
                $ret .= $sep_last;
            }

            $ret .= $array[$i];
        }

        return $ret;
    }

    /**
     * Formats a localised message with passed parameters
     *
     * @param string $key  string identifier of message
     * @param array  $args Parameters to substitute in
     *
     * @return string localised message
     * @todo Implement conditionals? Right now, some messages make
     *     reference to line numbers, but those aren't always available
     */
    public function formatMessage(string $key, array $args = []): string
    {
        if (!$this->loaded) {
            $this->load();
        }

        if (!isset($this->messages[$key])) {
            return "[$key]";
        }

        $raw = $this->messages[$key];
        $subst = [];
        $generator = false;
        foreach ($args as $i => $value) {
            if (\is_object($value)) {
                if ($value instanceof Token) {
                    // factor this out some time
                    if (!$generator) {
                        $generator = $this->context->get('Generator');
                    }

                    if (isset($value->name)) {
                        $subst["\$${i}.Name"] = $value->name;
                    }

                    if (isset($value->data)) {
                        $subst["\$${i}.Data"] = $value->data;
                    }

                    $subst["\$${i}.Compact"] =
                    $subst["\$${i}.Serialized"] = $generator->generateFromToken($value);

                    // a more complex algorithm for compact representation
                    // could be introduced for all types of tokens. This
                    // may need to be factored out into a dedicated class
                    if ($value instanceof Tag && !empty($value->attr)) {
                        $stripped_token = clone $value;
                        $stripped_token->attr = [];

                        $subst['$' . $i . '.Compact'] = $generator->generateFromToken($stripped_token);
                    }
                    $subst['$' . $i . '.Line'] = $value->line ?: 'unknown';
                }

                continue;
            }

            if (\is_array($value)) {
                $keys = array_keys($value);
                if (array_keys($keys) === $keys) {
                    // list
                    $subst['$' . $i] = $this->listify($value);
                } else {
                    // associative array
                    // no $i implementation yet, sorry
                    $subst['$' . $i . '.Keys'] = $this->listify($keys);
                    $subst['$' . $i . '.Values'] = $this->listify(array_values($value));
                }

                continue;
            }

            $subst['$' . $i] = $value;
        }

        return strtr($raw, $subst);
    }
}
