<?php

declare(strict_types=1);

namespace HTMLPurifier;

/*! @mainpage
 *
 * HTML Purifier is an HTML filter that will take an arbitrary snippet of
 * HTML and rigorously test, validate and filter it into a version that
 * is safe for output onto webpages. It achieves this by:
 *
 *  -# Lexing (parsing into tokens) the document,
 *  -# Executing various strategies on the tokens:
 *      -# Removing all elements not in the whitelist,
 *      -# Making the tokens well-formed,
 *      -# Fixing the nesting of the nodes, and
 *      -# Validating attributes of the nodes; and
 *  -# Generating HTML from the purified tokens.
 *
 * However, most users will only need to interface with the HTMLPurifier
 * and \HTMLPurifier\Config.
 */

/*
    HTML Purifier 4.12.0 - Standards Compliant HTML Filtering
    Copyright (C) 2006-2008 Edward Z. Yang

    This library is free software; you can redistribute it and/or
    modify it under the terms of the GNU Lesser General Public
    License as published by the Free Software Foundation; either
    version 2.1 of the License, or (at your option) any later version.

    This library is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
    Lesser General Public License for more details.

    You should have received a copy of the GNU Lesser General Public
    License along with this library; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 */

use HTMLPurifier\Encoder;
use HTMLPurifier\Context;
use HTMLPurifier\Filter;
use HTMLPurifier\ErrorCollector;
use HTMLPurifier\Generator;
use HTMLPurifier\Exception;
use HTMLPurifier\Lexer;
use HTMLPurifier\LanguageFactory;
use HTMLPurifier\IDAccumulator;
use HTMLPurifier\Strategy\Core;

/**
 * Facade that coordinates HTML Purifier's subsystems in order to purify HTML.
 *
 * @note There are several points in which configuration can be specified
 *       for HTML Purifier.  The precedence of these (from lowest to
 *       highest) is as follows:
 *          -# Instance: new HTMLPurifier($config)
 *          -# Invocation: purify($html, $config)
 *       These configurations are entirely independent of each other and
 *       are *not* merged (this behavior may change in the future).
 *
 * @todo We need an easier way to inject strategies using the configuration
 *       object.
 */
class HTMLPurifier
{

    /**
     * Version of HTML Purifier.
     *
     * @type string
     */
    public $version = '4.12.0';

    /**
     * Constant with version of HTML Purifier.
     */
    const VERSION = '4.12.0';

    /**
     * Global configuration object.
     *
     * @type \HTMLPurifier\Config
     */
    public $config;

    /**
     * Array of extra filter objects to run on HTML,
     * for backwards compatibility.
     *
     * @type Filter[]
     */
    private $filters = [];

    /**
     * Single instance of HTML Purifier.
     *
     * @type HTMLPurifier
     */
    private static $instance;

    /**
     * @type Core
     */
    protected $strategy;

    /**
     * @type Generator
     */
    protected $generator;

    /**
     * Resultant context of last run purification.
     * Is an array of contexts if the last called method was purifyArray().
     *
     * @type Context
     */
    public $context;

    /**
     * Initializes the purifier.
     *
     * @param \HTMLPurifier\Config|mixed $config Optional \HTMLPurifier\Config object
     *                                          for all instances of the purifier, if omitted, a default
     *                                          configuration is supplied (which can be overridden on a
     *                                          per-use basis).
     *                                          The parameter can also be any type that
     *                                          \HTMLPurifier\Config::create() supports.
     */
    public function __construct($config = null)
    {
        $this->config = \HTMLPurifier\Config::create($config);
        $this->strategy = new Core();
    }

    /**
     * Adds a filter to process the output. First come first serve
     *
     * @param Filter $filter HTMLPurifier\HTMLPurifier_Filter object
     */
    public function addFilter($filter): void
    {
        trigger_error(
            'HTMLPurifier->addFilter() is deprecated, use configuration directives' .
            ' in the Filter namespace or Filter.Custom',
            E_USER_WARNING
        );

        $this->filters[] = $filter;
    }

    /**
     * Filters an HTML snippet/document to be XSS-free and standards-compliant.
     *
     * @param string              $html   String of HTML to purify
     * @param \HTMLPurifier\Config $config Config object for this operation,
     *                                    if omitted, defaults to the config object specified during this
     *                                    object's construction. The parameter can also be any type
     *                                    that \HTMLPurifier\Config::create() supports.
     *
     * @return string Purified HTML
     * @throws Exception
     */
    public function purify(string $html, ?\HTMLPurifier\Config $config = null): ?string
    {
        // :TODO: make the config merge in, instead of replace
        $config = $config ? \HTMLPurifier\Config::create($config) : $this->config;

        // implementation is partially environment dependant, partially
        // configuration dependant
        $lexer = Lexer::create($config);

        $context = new Context();

        // setup HTML generator
        $this->generator = new Generator($config, $context);
        $context->register('Generator', $this->generator);

        // set up global context variables
        if ($config->get('Core.CollectErrors')) {
            // may get moved out if other facilities use it
            $language_factory = LanguageFactory::instance();
            $language = $language_factory->create($config, $context);
            $context->register('Locale', $language);

            $error_collector = new ErrorCollector($context);
            $context->register('ErrorCollector', $error_collector);
        }

        // setup id_accumulator context, necessary due to the fact that
        // AttrValidator can be called from many places
        $id_accumulator = IDAccumulator::build($config, $context);
        $context->register('IDAccumulator', $id_accumulator);

        $html = Encoder::convertToUTF8($html, $config, $context);

        // setup filters
        $filter_flags = $config->getBatch('Filter');
        $custom_filters = $filter_flags['Custom'];
        unset($filter_flags['Custom']);
        $filters = [];
        foreach ($filter_flags as $filter => $flag) {
            if (!$flag) {
                continue;
            }
            if (strpos($filter, '.') !== false) {
                continue;
            }
            $class = "HTMLPurifier_Filter_$filter";
            $filters[] = new $class;
        }
        foreach ($custom_filters as $filter) {
            // maybe "HTMLPurifier_Filter_$filter", but be consistent with AutoFormat
            $filters[] = $filter;
        }
        $filters = array_merge($filters, $this->filters);
        // maybe prepare(), but later

        $filter_size = count($filters);
        foreach ($filters as $filter) {
            $html = $filter->preFilter($html, $config, $context);
        }

        // purified HTML
        $html =
            $this->generator->generateFromTokens(
            // list of tokens
                $this->strategy->execute(
                // list of un-purified tokens
                    $lexer->tokenizeHTML(
                    // un-purified HTML
                        $html,
                        $config,
                        $context
                    ),
                    $config,
                    $context
                )
            );

        for ($i = $filter_size - 1; $i >= 0; $i--) {
            $html = $filters[$i]->postFilter($html, $config, $context);
        }

        $html = Encoder::convertFromUTF8($html, $config, $context);
        $this->context =& $context;

        return $html;
    }

    /**
     * Filters an array of HTML snippets
     *
     * @param string[]            $array_of_html Array of html snippets
     * @param \HTMLPurifier\Config $config        Optional config object for this operation.
     *                                           See HTMLPurifier::purify() for more details.
     *
     * @return string[] Array of purified HTML
     * @throws Exception
     */
    public function purifyArray(array $array_of_html, ?\HTMLPurifier\Config $config = null): array
    {
        $context_array = [];
        $array = [];

        foreach ($array_of_html as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->purifyArray($value, $config);
            } else {
                $array[$key] = $this->purify($value, $config);
            }
            $context_array[$key] = $this->context;
        }

        $this->context = $context_array;

        return $array;
    }

    /**
     * Singleton for enforcing just one HTML Purifier in your system
     *
     * @param HTMLPurifier|\HTMLPurifier\Config $prototype Optional prototype
     *                                                    HTMLPurifier instance to overload singleton with,
     *                                                    or \HTMLPurifier\Config instance to configure the
     *                                                    generated version with.
     *
     * @return HTMLPurifier
     */
    public static function instance($prototype = null): HTMLPurifier
    {
        if (!self::$instance || $prototype) {
            if ($prototype instanceof static) {
                self::$instance = $prototype;
            } elseif ($prototype) {
                self::$instance = new static($prototype);
            } else {
                self::$instance = new static();
            }
        }

        return self::$instance;
    }

    /**
     * Singleton for enforcing just one HTML Purifier in your system
     *
     * @param HTMLPurifier|\HTMLPurifier\Config $prototype Optional prototype
     *                                                    HTMLPurifier instance to overload singleton with,
     *                                                    or \HTMLPurifier\Config instance to configure the
     *                                                    generated version with.
     *
     * @return HTMLPurifier
     * @note Backwards compatibility, see instance()
     */
    public static function getInstance($prototype = null): HTMLPurifier
    {
        return static::instance($prototype);
    }
}
