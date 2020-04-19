<?php

declare(strict_types=1);

// OUT OF DATE, NEEDS UPDATING!
// USE XMLWRITER!
use HTMLPurifier\Encoder;
use HTMLPurifier\Context;
use HTMLPurifier\Generator;
use HTMLPurifier\Exception;
use HTMLPurifier\Token\End;
use HTMLPurifier\Token\EmptyToken;
use HTMLPurifier\Token\Text;
use HTMLPurifier\Token\Start;

/**
 * Class HTMLPurifier_Printer
 */
class HTMLPurifier_Printer
{
    /**
     * For HTML generation convenience funcs.
     *
     * @type Generator
     */
    protected $generator;

    /**
     * For easy access.
     *
     * @type HTMLPurifier_Config
     */
    protected $config;

    /**
     * Give generator necessary configuration if possible
     *
     * @param HTMLPurifier_Config $config
     *
     * @throws Exception
     */
    public function prepareGenerator(HTMLPurifier_Config $config): void
    {
        $all = $config->getAll();

        $context = new Context();
        $this->generator = new Generator($config, $context);
    }

    /**
     * Returns a start tag
     *
     * @param string $tag  Tag name
     * @param array  $attr Attribute array
     *
     * @return string
     */
    protected function start(string $tag, array $attr = []): string
    {
        return $this->generator->generateFromToken(
            new Start($tag, $attr ?: [])
        );
    }

    /**
     * Returns an end tag
     *
     * @param string $tag Tag name
     *
     * @return string
     */
    protected function end(string $tag): string
    {
        return $this->generator->generateFromToken(
            new End($tag)
        );
    }

    /**
     * Prints a complete element with content inside
     *
     * @param string $tag      Tag name
     * @param string $contents Element contents
     * @param array  $attr     Tag attributes
     * @param bool   $escape   whether or not to escape contents
     *
     * @return string
     */
    protected function element(string $tag, string $contents, array $attr = [], bool $escape = true): string
    {
        return $this->start($tag, $attr) .
               ($escape ? $this->escape($contents) : $contents) .
               $this->end($tag);
    }

    /**
     * @param string $tag
     * @param array  $attr
     *
     * @return string
     */
    protected function elementEmpty(string $tag, array $attr = []): string
    {
        return $this->generator->generateFromToken(
            new EmptyToken($tag, $attr)
        );
    }

    /**
     * @param string $text
     *
     * @return string
     */
    protected function text(string $text): string
    {
        return $this->generator->generateFromToken(
            new Text($text)
        );
    }

    /**
     * Prints a simple key/value row in a table.
     *
     * @param string $name  Key
     * @param mixed  $value Value
     *
     * @return string
     */
    protected function row(string $name, $value): string
    {
        if (is_bool($value)) {
            $value = $value ? 'On' : 'Off';
        }

        return
            $this->start('tr') . "\n" .
            $this->element('th', $name) . "\n" .
            $this->element('td', $value) . "\n" .
            $this->end('tr');
    }

    /**
     * Escapes a string for HTML output.
     *
     * @param string $string String to escape
     *
     * @return string
     */
    protected function escape(string $string): string
    {
        return htmlspecialchars(
            Encoder::cleanUTF8($string),
            ENT_COMPAT,
            'UTF-8'
        );
    }

    /**
     * Takes a list of strings and turns them into a single list
     *
     * @param string[] $array  List of strings
     * @param bool     $polite Bool whether or not to add an end before the last
     *
     * @return string
     */
    protected function listify(array $array, bool $polite = false): string
    {
        if (empty($array)) {
            return 'None';
        }

        $ret = '';
        $i = count($array);

        foreach ($array as $value) {
            $i--;
            $ret .= $value;

            if ($i > 0 && !($polite && $i === 1)) {
                $ret .= ', ';
            }

            if ($polite && $i === 1) {
                $ret .= 'and ';
            }
        }

        return $ret;
    }

    /**
     * Retrieves the class of an object without prefixes, as well as metadata
     *
     * @param object $obj        Object to determine class of
     * @param string $sec_prefix Further prefix to remove
     *
     * @return string
     */
    protected function getClass($obj, string $sec_prefix = ''): string
    {
        static $five = null;
        if ($five === null) {
            $five = PHP_VERSION_ID >= 50000;
        }

        $prefix = 'HTMLPurifier_' . $sec_prefix;

        if (!$five) {
            $prefix = strtolower($prefix);
        }

        $class = str_replace($prefix, '', get_class($obj));
        $lclass = strtolower($class);
        $class .= '(';

        switch ($lclass) {
            case 'enum':
                $values = [];
                foreach ($obj->valid_values as $value => $bool) {
                    $values[] = $value;
                }
                $class .= implode(', ', $values);
                break;
            case 'css_composite':
                $values = [];
                foreach ($obj->defs as $def) {
                    $values[] = $this->getClass($def, $sec_prefix);
                }
                $class .= implode(', ', $values);
                break;
            case 'css_multiple':
                $class .= $this->getClass($obj->single, $sec_prefix) . ', ';
                $class .= $obj->max;
                break;
            case 'css_denyelementdecorator':
                $class .= $this->getClass($obj->def, $sec_prefix) . ', ';
                $class .= $obj->element;
                break;
            case 'css_importantdecorator':
                $class .= $this->getClass($obj->def, $sec_prefix);
                if ($obj->allow) {
                    $class .= ', !important';
                }
                break;
        }

        return $class . ')';
    }
}
