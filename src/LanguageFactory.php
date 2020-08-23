<?php

declare(strict_types=1);

namespace HTMLPurifier;

use HTMLPurifier\AttrDef\Lang;

/**
 * Class responsible for generating HTMLPurifier\HTMLPurifier_Language objects, managing
 * caching and fallbacks.
 *
 * @note Thanks to MediaWiki for the general logic, although this version
 *       has been entirely rewritten
 * @todo Serialized cache for languages
 */
class LanguageFactory
{
    /**
     * Cache of language code information used to load HTMLPurifier\HTMLPurifier_Language objects.
     * Structure is: $factory->cache[$language_code][$key] = $value
     *
     * @var array
     */
    public $cache = [];

    /**
     * Valid keys in the HTMLPurifier\HTMLPurifier_Language object. Designates which
     * variables to slurp out of a message file.
     *
     * @var array
     */
    public $keys = ['fallback', 'messages', 'errorNames'];

    /**
     * Instance to validate language codes.
     *
     * @var Lang
     *
     */
    protected $validator;

    /**
     * Cached copy of dirname(__FILE__), directory of current file without
     * trailing slash.
     *
     * @var string
     */
    protected $dir;

    /**
     * Keys whose contents are a hash map and can be merged.
     *
     * @var array
     */
    protected $mergeable_keys_map = ['messages' => true, 'errorNames' => true];

    /**
     * Keys whose contents are a list and can be merged.
     *
     * @value array lookup
     */
    protected $mergeable_keys_list = [];

    /**
     * Retrieve sole instance of the factory.
     *
     * @param LanguageFactory|true|null $prototype    Optional prototype to overload sole instance with,
     *                                                or bool true to reset to default factory.
     *
     * @return LanguageFactory
     */
    public static function instance($prototype = null): LanguageFactory
    {
        static $instance = null;
        if ($prototype !== null && $prototype !== true) {
            $instance = $prototype;
        } elseif ($instance === null || $prototype === true) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Private constructor, use instance() to get the singleton.
     */
    private function __construct()
    {
        $this->validator = new Lang();
        $this->dir = HTMLPURIFIER_PREFIX . '/';
    }

    /**
     * Creates a language object, handles class fallbacks
     *
     * @param Config      $config
     * @param Context     $context
     * @param bool|string $code Code to override configuration with. Private parameter.
     *
     * @return Language
     * @throws Exception
     */
    public function create(Config $config, Context $context, $code = false): Language
    {
        // validate language code
        if ($code === false) {
            $code = $this->validator->validate(
                $config->get('Core.Language'),
                $config,
                $context
            );
        } else {
            $code = $this->validator->validate((string)$code, $config, $context);
        }

        if (!\is_string($code)) {
            $code = 'en'; // malformed code becomes English
        }

        $pcode = str_replace('-', '_', $code); // make valid PHP classname
        static $depth = 0; // recursion protection

        if ($code === 'en') {
            $lang = new Language($config, $context);
        } else {
            $class = 'HTMLPurifier_Language_' . $pcode;
            $file = $this->dir . '/Language/classes/' . $code . '.php';

            if (file_exists($file) || class_exists($class, false)) {
                $lang = new $class($config, $context);

                if (!$lang instanceof Language) {
                    throw new Exception('Language could not be loaded');
                }
            } else {
                // Go fallback
                $raw_fallback = $this->getFallbackFor($code);
                $fallback = $raw_fallback ?: 'en';
                $depth++;
                $lang = $this->create($config, $context, $fallback);

                if (!$raw_fallback) {
                    $lang->error = true;
                }

                $depth--;
            }
        }

        $lang->code = $code;

        return $lang;
    }

    /**
     * Returns the fallback language for language
     *
     * @note Loads the original language into cache
     *
     * @param string $code language code
     *
     * @return string|bool
     */
    public function getFallbackFor(string $code)
    {
        $this->loadLanguage($code);

        return $this->cache[$code]['fallback'];
    }

    /**
     * Loads language into the cache, handles message file and fallbacks
     *
     * @param string $code language code
     */
    public function loadLanguage(string $code): void
    {
        static $languages_seen = []; // recursion guard

        // abort if we've already loaded it
        if (isset($this->cache[$code])) {
            return;
        }

        // generate filename
        $filename = "{$this->dir}/Language/messages/${code}.php";

        // default fallback : may be overwritten by the ensuing include
        $fallback = ($code !== 'en') ? 'en' : false;

        // load primary localisation
        if (!file_exists($filename)) {
            // skip the include: will rely solely on fallback
            $filename = $this->dir . '/Language/messages/en.php';
            $cache = [];
        } else {
            include $filename;
            $cache = compact($this->keys);
        }

        // load fallback localisation
        if (!empty($fallback)) {
            // infinite recursion guard
            if (isset($languages_seen[$code])) {
                throw new Exception("Circular fallback reference in language {$code}");
            }

            $languages_seen[$code] = true;

            // load the fallback recursively
            $this->loadLanguage($fallback);
            $fallback_cache = $this->cache[$fallback];

            // merge fallback with current language
            foreach ($this->keys as $key) {
                if (isset($cache[$key], $fallback_cache[$key])) {
                    if (isset($this->mergeable_keys_map[$key])) {
                        $cache[$key] += $fallback_cache[$key];
                    } elseif (isset($this->mergeable_keys_list[$key])) {
                        $cache[$key] = array_merge($fallback_cache[$key], $cache[$key]);
                    }
                } else {
                    $cache[$key] = $fallback_cache[$key];
                }
            }
        }

        // save to cache for later retrieval
        $this->cache[$code] = $cache;

        return;
    }
}
