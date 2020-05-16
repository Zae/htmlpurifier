<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef;

use HTMLPurifier\AttrDef;
use \HTMLPurifier\Config;
use HTMLPurifier\Context;
use HTMLPurifier\Exception;

/**
 * Validates the HTML attribute style, otherwise known as CSS.
 *
 * @note We don't implement the whole CSS specification, so it might be
 *       difficult to reuse this component in the context of validating
 *       actual stylesheet declarations.
 * @note If we were really serious about validating the CSS, we would
 *       tokenize the styles and then parse the tokens. Obviously, we
 *       are not doing that. Doing that could seriously harm performance,
 *       but would make these components a lot more viable for a CSS
 *       filtering solution.
 */
class CSS extends AttrDef
{
    /**
     * @param string              $css
     * @param \HTMLPurifier\Config $config
     * @param Context             $context
     *
     * @return bool|string
     * @throws \HTMLPurifier\Exception
     */
    public function validate($css, $config, $context)
    {
        $css = $this->parseCDATA($css);

        $definition = $config->getCSSDefinition();
        $allow_duplicates = $config->get('CSS.AllowDuplicates');

        // According to the CSS2.1 spec, the places where a
        // non-delimiting semicolon can appear are in strings
        // escape sequences.   So here is some dumb hack to
        // handle quotes.
        $len = \strlen($css);
        $accum = '';
        $declarations = [];
        $quoted = false;

        for ($i = 0; $i < $len; $i++) {
            $c = strcspn($css, ";'\"", $i);
            $accum .= substr($css, $i, $c);
            $i += $c;

            if ($i === $len) {
                break;
            }

            $d = $css[$i];
            if ($quoted) {
                $accum .= $d;
                if ($d === $quoted) {
                    $quoted = false;
                }
            } else {
                if ($d === ';') {
                    $declarations[] = $accum;
                    $accum = '';
                } else {
                    $accum .= $d;
                    $quoted = $d;
                }
            }
        }

        if ($accum !== '') {
            $declarations[] = $accum;
        }

        $propvalues = [];
        $new_declarations = '';

        /**
         * Name of the current CSS property being validated.
         */
        $property = false;
        $context->register('CurrentCSSProperty', $property);

        foreach ($declarations as $declaration) {
            if (!$declaration) {
                continue;
            }

            if (!strpos($declaration, ':')) {
                continue;
            }

            [$property, $value] = explode(':', $declaration, 2);
            $property = trim($property);
            $value = trim($value);
            $ok = false;

            do {
                if (isset($definition->info[$property])) {
                    $ok = true;
                    break;
                }
                if (ctype_lower($property)) {
                    break;
                }
                $property = strtolower($property);
                if (isset($definition->info[$property])) {
                    $ok = true;
                    break;
                }
            } while (0);

            if (!$ok) {
                continue;
            }

            // inefficient call, since the validator will do this again
            if (strtolower(trim($value)) !== 'inherit') {
                // inherit works for everything (but only on the base property)
                $result = false;
                if (isset($definition->info, $definition->info[$property])) {
                    $result = $definition->info[$property]->validate(
                        $value,
                        $config,
                        $context
                    ) ?? false;
                }

            } else {
                $result = 'inherit';
            }

            if ($result === false) {
                continue;
            }

            if ($allow_duplicates) {
                $new_declarations .= "$property:$result;";
            } else {
                $propvalues[$property] = $result;
            }
        }

        $context->destroy('CurrentCSSProperty');

        // procedure does not write the new CSS simultaneously, so it's
        // slightly inefficient, but it's the only way of getting rid of
        // duplicates. Perhaps config to optimize it, but not now.

        foreach ($propvalues as $prop => $value) {
            $new_declarations .= "$prop:$value;";
        }

        return $new_declarations ?: false;
    }
}
