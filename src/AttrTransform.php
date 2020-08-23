<?php

declare(strict_types=1);

namespace HTMLPurifier;

/**
 * Processes an entire attribute array for corrections needing multiple values.
 *
 * Occasionally, a certain attribute will need to be removed and popped onto
 * another value.  Instead of creating a complex return syntax for
 * HTMLPurifier_AttrDef, we just pass the whole attribute array to a
 * specialized object and have that do the special work.  That is the
 * family of HTMLPurifier\HTMLPurifier_AttrTransform.
 *
 * An attribute transformation can be assigned to run before or after
 * HTMLPurifier_AttrDef validation.  See HTMLPurifier\HTMLPurifier_HTMLDefinition for
 * more details.
 */
abstract class AttrTransform
{
    /**
     * Abstract: makes changes to the attributes dependent on multiple values.
     *
     * @param array   $attr                 Assoc array of attributes, usually from
     *                                      HTMLPurifier\Token\HTMLPurifier_Token_Tag::$attr
     * @param Config  $config               Mandatory \HTMLPurifier\Config object.
     * @param Context $context              Mandatory HTMLPurifier\HTMLPurifier_Context object
     *
     * @return array Processed attribute array.
     */
    abstract public function transform(array $attr, Config $config, Context $context): array;

    /**
     * Prepends CSS properties to the style attribute, creating the
     * attribute if it doesn't exist.
     *
     * @param array $attr Attribute array to process (passed by reference)
     * @psalm-param string[] $attr
     * @phpstan-param string[] $attr
     * @param string $css  CSS to prepend
     */
    public function prependCSS(array &$attr, string $css): void
    {
        $attr['style'] = $attr['style'] ?? '';
        $attr['style'] = $css . $attr['style'];
    }

    /**
     * Retrieves and removes an attribute
     *
     * @param array $attr Attribute array to process (passed by reference)
     * @param mixed $key  Key of attribute to confiscate
     *
     * @psalm-param string[] $attr
     * @phpstan-param string[] $attr
     *
     * @return mixed
     */
    public function confiscateAttr(array &$attr, $key)
    {
        if (!isset($attr[$key])) {
            return null;
        }

        $value = $attr[$key];
        unset($attr[$key]);

        return $value;
    }
}
